import argparse

parser = argparse.ArgumentParser()
parser.add_argument('--flip',action="store_true",default=False,help='Default is reference genome (the one the qteller database is being built for) first. Comparator genome sequence. Use --flip if your file is the other way around')
parser.add_argument('syn_file',help="The output of SynMap downloaded from the XXX link.")
args = parser.parse_args()
fh = open(args.syn_file)
for x in fh:
    if x[0] == '#': continue
    y = x.strip().split()
    z1 = y[3]
    z2 = y[7]
    if args.flip:
        z1,z2 = z2,z1
    gene1 = z1.split('||')[3]
    gene2 = z2.split('||')[3]
    print "{0}\t{1}".format(gene1,gene2)
