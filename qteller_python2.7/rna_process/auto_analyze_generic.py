#Written by James Schnable (jschnable@berkeley.edu)

#Warning: (To PIs) This script is NOT a replacement for a trained scientist 
#who knows enough to think critically about analyses and troubleshoot 
#unexpected results.

#These are the default options used for GSNAP. But modifying or adding to the dictionary below you can change the parameters used for all aligments.
gsnap_dict = {

#How RAM intensive should GSNAP be? Range from 0-5.
'-B':'5',

'-D':'defined in config file',#Don't change
'-d':'defined in config file',#Don't change
'--nthreads':'defined in config file',#Don't change

#maximum number of alignments to print per read. 
#Larger numbers result in bigger intermediate files.
'-n':'10',

#Don't change!
'--format=sam':'',

#
'-Q':'',

#Whether to use GMAP to improve alignments. Time intensive if turned on. 
#For details see GSNAP documentation
'--gmap-mode=none':'',

#Skip printing transcripts which don't align. Saves space and
#has zero effect on the final gene expression data
'--nofails':'',

#Maximum mismatches allowed. For details see GSNAP documentation
'-m':'3',

#the default is usually safe here. 
#Older Illumina sequence data (<= CASSAVA 1.7) might use a different type of quality encodings.
#Change to "--quality-protocol=illumina" if many reads have runs of "BBBBB" at the end of their quality strings.
'--quality-protocol=sanger':'',

#Add additional options here if you like. 
#Format:
#'command-line-option':'value-if-any',
}

#Real code begins here

#reads the options out of the specified configuration file
def config_parse(fname):
    if not os.path.exists(fname):
        sys.exit("Config file {0} does not exist.\nDying now.\n".format(fname))
    fh = open(fname)
    results = {}
    for x in fh:
        if x[0] == '#': continue
        y = x.strip().replace('"','').split('=')
        if len(y) < 2: continue
        results[y[0]] = y[1]
    return results

#reads in the list of experiments + fastq files to
#figure out what data needs to be analyzed
def data_ss_parse(fname):
    if not os.path.exists(fname):
        sys.exit("Expression spreadsheet {0} does not exist.\nDying now.\n".format(fname))
    fh = open(fname)
    datasets = {}
    for x in fh:
        print x
        y = x.strip().replace('"','').split(',')
        if y[0] == 'ExpID': continue
        if y[0] == '': continue
        myname = y[0]
        datasets[myname] = y[1:]
    return datasets

def test_file(an_opt,myopts):
    if not an_opt in myopts or not os.path.exists(myopts[an_opt]):
        sys.exit("{0} is not provided or points to an invalid file/directory. Dying now\n".format(an_opt))

#sanity checks. Are all the files needed specified? Do they exist? That sort of thing.
def test_opts(myopts):
    if not 'genomic' in myopts or not myopts['genomic'] in set(['True','False']):
        sys.exit("You need to tell me if you are aligning to a transcriptome or a genome by setting \"genomic=True\" or \"genomic=False\" in the config file. Dying now.")
    if not 'max_cpus' in myopts:
        general_log_file.write("Max_CPUs not specified. Attempting to determine manually...")
        import multiprocessing
        cpu_count = multiprocessing.cpu_count()
        if cpu_count < 8:
            myopts['max_cpus'] = str(cpu_count)
        else:
            myopts['max_cpus'] = str(cpu_count) - 2
        general_log_file.write("Max_CPUs set to {0}.\nThis may either be wastefully slow or melt your computer. Don't say I didn't warn you.\n".format(myopts['max_cpus']))
    else:
        try:
            int(myopts['max_cpus'])
        except:
            sys.exit("The value you provided for max_cpus ({0}) isn't an integer (1,2,3,100,etc). Dying now.\n".format(myopts['max_cpus']))
    test_file('gsnap_index_directory',myopts)
    if myopts['genomic'] == 'True':
        test_file('splice_file',myopts)
        test_file('gtf_file',myopts)
    elif myopts['genomic'] == 'False':
        test_file('index_fasta_file',myopts)
    test_file('data_spreadsheet',myopts)
    if not 'trim_reads' in myopts: myopts['trim_reads'] = False
    else: myopts['trim_reads'] = True
    if myopts['trim_reads'] == True:
        if not 'qual_cut' in myopts: myopts['qual_cut'] = 20
        #this is a very conservative default in case people run qTeller on
        #data from old 32 bp sequencing runs
        if not 'min_length' in myopts: myopts['min_length'] = 25
        #3' adapter from TruSeq up to the start of the variable index region
        if not 'adapter_seq' in myopts: myopts['adapter_seq'] = 'GATCGGAAGAGCACACGTCTGAACTCCAGTCAC'
    return myopts
import subprocess as sp
import os,sys
#file to track the progress of the analysis and report any problems
#general_log_file = open('auto_analyze_runtime.log','w',0)

#user can provide a config file of their own if they want
if len(sys.argv) > 1:
    myconfig_file = sys.argv[1]
    sys.stderr.write('Config file provided by user: {0}\n'.format(sys.argv[1]))
else:
    myconfig_file = './config_file'
    sys.stderr.write("No config file provided. Using default: {0}\n".format(myconfig_file))
#parsing the variables specified in the config file
myopts = config_parse(myconfig_file)
#makes sure enough information was provided in the config file
myopts = test_opts(myopts)
#create a list of datasets to be aligned
datasets = data_ss_parse(myopts['data_spreadsheet'])
#more logging
sys.stderr.write("Datasets to analyze:\n")
for d in datasets:
    sys.stderr.write("{0}: {1}\n".format(d,','.join(datasets[d])))
    for x in datasets[d]:
        if not os.path.exists(x):
            sys.exit("File {0} from dataset {1} couldn't be found. Dying now to save you from coming back to a crashed script hours from now.\n".format(x,d))

#setting up the gsnap command that will be used for all alignments
snaplist_base = ['gsnap']
snaplist_base.extend(['-D',myopts['gsnap_index_directory']])
snaplist_base.extend(['-d',myopts['gsnap_index_name']])
snaplist_base.append('--nthreads={0}'.format(myopts['max_cpus']))
if myopts['genomic'] == 'True':
    snaplist_base.extend(['-s',myopts['splice_file']])
for x in gsnap_dict:
    if 'config' in gsnap_dict[x]: continue
    if not gsnap_dict[x]:
        snaplist_base.append(x)
    else:
        snaplist_base.append(x)
        snaplist_base.append(gsnap_dict[x])


#The main loop. Runs once per dataset
for x in datasets:

    #if more than one fastq file was provided for a single dataset combine them into a single file for alignment
    if len(datasets[x]) > 1:
        clist = ['cat']
        clist.extend(datasets[x])
        tfile = open('temp.fastq','w')
        proc = sp.Popen(clist,stdout=tfile)
        proc.wait()
        fastq_file = 'temp.fastq'
    else:
        fastq_file = datasets[x][0]

    #sets up the names of various temporary files and log files
    one_hits = x + "-unsorted"
    two_hits = x + ".bam"
    three_hits = x + "-sorted"
    log_file = x + ".log"
    fh_results = open(one_hits,'w')
    fh_log = open(log_file,'w')

    #we preserve the unique extensions of cufflinks and eXpress 
    if myopts['genomic'] == 'True':
        final_file = x + ".fpkm_tracking"
    elif myopts['genomic'] == 'False':
        final_file = x + ".xprs"
    else:
        sys.exit("Invalid choice for option \"genomic\": {0}\nValid options are: True and False.\nDying Now.".format(myopts['genomic']))

    #trim reads for adapters and low quality sequences if requested in options file
    if myopts['trim_reads'] == True:
        mystub = fastq_file.replace('.fastq','')
        mytrimmed_fastq = mystub + ".trimmed.fastq"
        tfile = open(mytrimmed_fastq,'w')
        trim_list = ['cutadapt','-q',myopts['qual_cut'],'-m',myopts['min_length'],'-a',myopts['adapter_seq'],fastq_file]
        sys.stderr.write("Now trimming {0}".format(x))
        proc = sp.Popen(map(str,trim_list),stdout=tfile,stderr=fh_log)
        proc.wait()
        tfile.close()
        if fastq_file == "temp.fastq":
            os.remove(fastq_file)
        fastq_file = mytrimmed_fastq

    #code to run the alignments
    snaplist = snaplist_base[:]
    snaplist.append(fastq_file)
    sys.stderr.write("{0}: Aligning...".format(x))
    proc = sp.Popen(snaplist,stdout=fh_results,stderr=fh_log)
    proc.wait()
    sys.stderr.write("Complete")
    fh_results.close()

    #if we created a temporary alignment file, delete it
    if myopts['trim_reads'] == True or fastq_file == 'temp.fastq':
        os.remove(fastq_file)

    #collect information on how many reads aligned to each sequence
    if myopts['stats_script_loc'] and os.path.exists(myopts['stats_script_loc']):
        proc = sp.Popen(['python',myopts['stats_script_loc'],one_hits],stdout=fh_log)
        proc.wait()
    else:
        sys.stderr.write("{0}: No location or invalid location for the stats script so no summary stats recorded in {1}. Continuing\n".format(x,log_file))
    fh_log.close()

    #convert alignment formats 
    fh_results = open(two_hits,'w')
    sys.stderr.write(" Converting to BAM...")
    proc = sp.Popen(['samtools','view','-bS',one_hits],stdout=fh_results)
    proc.wait()
    sys.stderr.write("Complete ")
    fh_results.close()
    if myopts['genomic'] == 'True':
        sys.stderr.write("Sorting BAM file...")
        proc = sp.Popen(['samtools','sort',two_hits,three_hits])
        proc.wait()
        sys.stderr.write("Complete ")
        os.remove(two_hits)
    if myopts['keep_sam_alignment_file'] != 'True':
        os.remove(one_hits)
    else:
        sys.stderr.write("\nNot saving SAM file because keep_sam_aligmnet_file is not set to \"True\"\n")

    #quantify gene expression using either cufflinks or eXpress
    if myopts['genomic'] == 'True':
        sys.stderr.write("Using Cufflinks to quantify gene expression because reads were aligned to a genome...")
        cf_list = ['cufflinks','-p',myopts['max_cpus'],'-u','--GTF',myopts['gtf_file'],three_hits+".bam"]
        proc = sp.Popen(cf_list)
        proc.wait()
        os.rename('genes.fpkm_tracking',final_file)
        if myopts['keep_sorted_bam_file'] != 'True':
            os.remove(three_hits+".bam")
        else:
            sys.stderr.write("Not saving SAM file because keep_sorted_bam_file is not set to \"True\"\n")
    else:
        sys.stderr.write("Using eXpress to quantify gene expression because reads were aligned to a transcriptome...")
        exp_list = ['express',myopts['index_fasta_file'],two_hits]
        proc = sp.Popen(exp_list)
        proc.wait()
        os.rename('results.xprs',final_file)
        os.remove(two_hits)
    #more record keeping
    sys.stderr.write("Quantification complete! Analysis complete! Expression values saved at {0}. Moving on...\n".format(final_file))
    
