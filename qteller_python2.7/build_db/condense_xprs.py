#In certain cases you may wish to use eXpress to assay expression at the genome level. The problem is that eXpress aligns to transcripts, not to genes. That means that 1) All the names are different from the gene names used for all your other data. 2) Sometimes expression levels will be split between multiple isoforms.
#To get around this problem this script takes an xprs file and a file mapping gene names to transcript names (can be generated from a gff file and "make_trascript2gene_table" and creates a new xprs formatted file, converting transcript names to gene names and adding together the expression of multiple isoforms of the same gene

import sys

if len(sys.argv) != 3:
    print """proper usage:
python condense_xprs.py (an xprs file) (a t2g file) > (new xprs file name)

t2g files can be generated using the make_trascript2gene_table.py script.

example usage:

python condense_xprs.py five_day_leaf.xprs maizev2FGS.t2g > five_day_leaf_condensed.xprs
"""
    1/0
xp_file = sys.argv[1]
t2g_file = sys.argv[2]
fh = open(xp_file)
header = fh.readline().strip()
xp_lines = {}
for x in fh:
    y = x.strip().split('\t')
    if len(y) < 2: continue
    xp_lines[y[1]] = y
print header
fh = open(t2g_file)
count = 0
for x in fh:
    y = x.strip().split('\t')
    if len(y) < 2: continue
    gene = y[0]
    transcripts = y[1].split(';')
    checked_t = []
    for t in transcripts:
        if t in xp_lines:
            checked_t.append(t)
    if len(checked_t) == 0: continue
    new_line = xp_lines[checked_t[0]]
    new_line[1] = gene
    new_line[2:13] = map(float,new_line[2:13])
    if len(checked_t) > 1:
        for t in checked_t[1:]:
            for z in range(2,13):
                new_line[z] += float(xp_lines[t][z])
    count += 1
    new_line[0] = count
    print "\t".join(map(str,new_line))
