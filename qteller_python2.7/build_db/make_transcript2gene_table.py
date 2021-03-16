def def_parse(astr):
    y = astr.split(';')
    mydefs = {}
    for z in y:
        a = z.split('=')
        if len(a) != 2: continue
        mydefs[a[0].lower()] = a[1]
    return mydefs

import argparse
parser = argparse.ArgumentParser()
parser.add_argument('gff_file',help='A gff file for a genome you wish to build a translation table of transcript names to gene names.')
parser.add_argument('--gene_def_tag',default='ID',help='The tag of the definition item at the gene level you wish to use as a name in the final output.')
parser.add_argument('--trans_def_tag',default='ID',help='The tag of the definition item at the transcript level you wish to use as a name in the final output.')
args = parser.parse_args()
gene2name = {}
transcript2gene = {}
fh = open(args.gff_file)
for x in fh:
    if x[0] == '#': continue
    y = x.strip().split('\t')
    if y[2] == 'gene':
        mydefs = def_parse(y[-1])
        if not args.gene_def_tag.lower() in mydefs or not 'id' in mydefs: continue
        gene2name[mydefs['id']] = mydefs[args.gene_def_tag.lower()]
    if 'RNA' in y[2]:
        mydefs = def_parse(y[-1])
        if not args.trans_def_tag.lower() in mydefs or not 'parent' in mydefs: continue
        transcript2gene[mydefs[args.trans_def_tag.lower()]] = mydefs['parent']

gene_clusters = {}
for x in transcript2gene:
    myname = x
    myparent = gene2name[transcript2gene[x]]
    if not myparent in gene_clusters: gene_clusters[myparent] = []
    gene_clusters[myparent].append(myname)


for g in sorted(list(gene_clusters)):
    gene_clusters[g].sort()
    print "{0}\t{1}".format(g,';'.join(gene_clusters[g]))
