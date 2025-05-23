import sys
import argparse
from datetime import datetime
#mysite = 'qteller3'

header = ['gene_name','chromosome','start']
parser = argparse.ArgumentParser(description="Retrieve genes from a specificed genomic interval.")
parser.add_argument("--chr")
parser.add_argument("--start")
parser.add_argument("--stop")
parser.add_argument("--filtered",action="store_true")
parser.add_argument("--link",action="store_true")
parser.add_argument("--included_vals")
args = parser.parse_args()


import sqlite3

db_file = '/var/www/html/qTeller/web_interface/gene_protein_qt4db'
mystart = int(args.start)
mystop = int(args.stop)
mychr = args.chr
header.extend(args.included_vals.split(','))
conn = sqlite3.connect(db_file)
conn.row_factory = sqlite3.Row
c = conn.cursor()
p_dict = {}
p_starts = {}
p_chrs = {}
fh2 = open('./tmp/%s.%i.%i.csv' % (mychr,mystart,mystop),'w')
fh3 = open("./tmp/%s.%i.%i.html" % (mychr,mystart,mystop),'w')
if args.link:
    syns = []
    for x in header:
        c.execute("select * from data_sets where stub_id=?",(x))
        for r in c:
            if r['type'] == 'Synteny':
                syns.append(x)
header2 = []
for h in header:
    header2.append(h.replace(" ",'_'))
if not 'all' in mychr and mystop != 0:
    c.execute("select * from protein_table where chromosome=? and stop>? and start <?",(mychr,mystart,mystop))
elif not 'all' in mychr and mystop == 0:
    c.execute("select * from protein_table where chromosome=? and stop>?", (mychr, mystart))
else:
    c.execute("select * from protein_table")
for row in c:
    myname = row['gene_name']
    if args.filtered:
        if row['filtered'] == 0: continue
    p_starts[myname] = int(row['start'])
    p_chrs[myname] = row['chromosome']
    if row['chromosome'] in set(['chrMt','chr0','chrPt']): continue
    p_dict[myname] = []
    for v in header:
        if not v in row:
            print (v)
        p_dict[myname].append(str(row[v]))
    p_dict[myname].append("<a href='http://qteller.maizegdb.org/Rna_Protein_bar_chart_B73v4.php?name=%s&protein=Submit for Protein Abundance!'>http://qteller.maizegdb.org/Rna_Protein_bar_chart_B73v4.php?name=%s&protein=Submit for Protein Abundance!</a>" % (myname,myname))
    
    if args.link:
        link_list = ["http://genomevolution.org/CoGe/GEvo.pl?"]
        link_list.append("show_cns=1")
        link_list.append("autogo=1")
        link_list.append("apply_all=30000")
        link_list.append("accn1=%s;mask1=non-genic" % row["gene_name"])
        s_count = 1
        for s in syns:
            if row[s] and row[s] != "''":
                s_count += 1
                link_list.append("accn%i=%s;mask%i=non-genic" % (s_count,row[s],s_count))
            else: continue
        if s_count > 1:
            link_list.append("num_seqs=%i" % s_count)
            p_dict[myname].append(";".join(link_list))
        else:
            p_dict[myname].append('No Syntelogs')
header2.append("Visualize_Expression_Link")
if args.link:
    header2.append("GEvo_Link")
fh2.write("This spreadsheet was generated using qTeller http://qteller.maizegdb.org on %s.\nSubmit all questions and concerns to Contact page.\nFor a list of the publications where these RNA-seq data were originally published visit http://qteller.maizegdb.org/rna_data_sources.php\nFor a description of the process used to generate these FPKM values see http://qteller.maizegdb.org/RNAseq-analysis-recipe.pdf\n" % (datetime.today()))
fh2.write(",".join(header2) + "\n")
fh3.write("<table border=\"1\"><tr><td>" + "</td> <td>".join(header2) + "</td></tr>")
genes = list(p_dict)
genes.sort(key=lambda g: p_starts[g])
for gene in sorted(genes,key=lambda g: p_chrs[g]):
    fh3.write("<tr><td>" + "</td> <td>".join(p_dict[gene]) + "</td></tr>")
    fh2.write(",".join(p_dict[gene]) + "\n")
fh3.write("</table>")
fh2.close()
fh3.close()
