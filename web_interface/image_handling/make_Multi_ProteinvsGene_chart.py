import math
from typing import TextIO

import numpy as np
import pandas as pd
import matplotlib.patches as mpatches

import matplotlib

matplotlib.use('Agg')
import matplotlib.pyplot as plt
import numpy as np
import sys
import argparse
import os

db_file = '/var/www/html/qTeller_python3/web_interface/proteindb'

width = .7


class qteller_db:
    def __init__(self, database):
        import sqlite3
        self.conn = sqlite3.connect(database)

    def stub2exp(self):
        import sqlite3
        results = {}
        self.conn.row_factory = sqlite3.Row
        c = self.conn.cursor()
        c.execute("select * from data_sets")
        for row in c:
            results[row['stub_id']] = row['experiment_id']
        return results

    def exp2data(self):
        import sqlite3
        results = {}
        self.conn.row_factory = sqlite3.Row
        c = self.conn.cursor()
        c.execute("select * from data_sets")
        for row in c:
            results[row['experiment_id']] = {'link': row['link'], 'desc': row['description']}
        return results

    def gene_exp(self, gene):
        import sqlite3
        results = {}
        self.conn.row_factory = sqlite3.Row
        c = self.conn.cursor()
        c.execute("select * from gene_exp_table where experiment_id=?", (gene,))
        for row in c:
            results[row['gene']] = {'exp': row['exp_val'], 'low': row['exp_val'], 'high': row['exp_val'],
                                    'source': row['source_id'], 'name': row['gene']}
        return results

    def pro_gene_exp(self, gene):
        import sqlite3
        results = {}
        self.conn.row_factory = sqlite3.Row
        c = self.conn.cursor()
        c.execute("select * from protein_exp_table where experiment_id=?", (gene,))
        for row in c:
            results[row['gene']] = {'exp': row['exp_val'], 'low': row['exp_val'], 'high': row['exp_val'],
                                    'source': row['source_id'], 'name': row['gene']}
        return results


def group_by_source(exps):
    sources = {}
    for e in exps:
        if not exps[e]['source'] in sources: sources[exps[e]['source']] = []
        sources[exps[e]['source']].append(exps[e])
    for x in sources:
        sources[x].sort(key=lambda val: val['name'])
    return sources


# configuring matplotlib
matplotlib.rcParams['xtick.major.size'] = 0
matplotlib.rcParams['xtick.minor.size'] = 0
matplotlib.rcParams['ytick.major.size'] = 0
matplotlib.rcParams['ytick.minor.size'] = 0
# matplotlib.rcParams['lines.linewidth'] = 3
matplotlib.rcParams['patch.linewidth'] = 2
matplotlib.rcParams['axes.linewidth'] = 2
matplotlib.rcParams['legend.fancybox'] = 'True'
matplotlib.rcParams['legend.shadow'] = 'True'
matplotlib.rcParams['legend.fontsize'] = 'small'
matplotlib.rcParams['legend.markerscale'] = .4
# xtick.major.size
# xtick.minor.size
# ytick.major.size
# ytick.minor.size
parser = argparse.ArgumentParser(description="Generate a gene expression bar chart.")
parser.add_argument('--names', dest='names', type=str, help="The name of the gene to draw on the x axis")
parser.add_argument('--exps', dest='exps', type=str,
                    help="A | separated list of the expression conditions to include in the figure.", default='')
parser.add_argument('--image_opts', dest='iopts',
                    help="A | separated list of options to change how the figure is drawn.", default='')
parser.add_argument('--dpi', help='The resolution of the figure to be drawn', default=300, type=int)
parser.add_argument('--ymax', type=int, default=0)
parser.add_argument('--xmax', type=int, default=0)
args = parser.parse_args()

mygene1 = args.names
genes = str(args.exps)

Nam1 = "Gene_" + mygene1 + "_GeneExpression"
Nam2 = "Gene_" + mygene1 + "_ProteinAbundance"

exps = args.exps.strip().split('dummy')
while("" in exps) :
    exps.remove("")
#print(exps)
mydb = qteller_db(db_file)
s2e = mydb.stub2exp()
exp2data = mydb.exp2data()
# print(exp2data)
# exp2data1 = mydb1.exp2data()
# exp2data2 = mydb2.exp2data()
myexp1 = mydb.gene_exp(mygene1)
# print(myexp1)

myexp2 = mydb.pro_gene_exp(mygene1)
# print(myexp2)

fh = open(('./tmp/%s-%s-%s-map.html' % (Nam1, Nam2, genes)), 'w')
fh5 = open(('./tmp/%s-%s-%s-map.txt' % (Nam1, Nam2, genes)), 'w')
data = 0
total_exps = 0
if (bool(myexp1) == True and bool(myexp2) == True):
    # print(myexp2)
    ymax = args.ymax
    xmax = args.xmax
    #print(len(exps))
    #print(str(exps))
    fh5.write(str(len(exps)))
    if len(exps) > 0:
        include_vals = exps
        t1, t2 = {}, {}
        for x in include_vals:
            fh5.write(str(x))
            #print(x)
            if x == '': continue
            if (x in list(myexp1.keys()) or x in list(myexp2.keys())):
                Source = myexp1[x]['source']
            if (x in list(myexp1.keys()) and x in list(myexp2.keys())):
                if x in list(myexp1.keys()):
                    t1[x] = myexp1[x]
                else:
                    t1[x] = {'exp': np.nan, 'low': np.nan, 'high': np.nan, 'source': None, 'name': x}

                if x in list(myexp2.keys()):
                    t2[x] = myexp2[x]
                else:
                    t2[x] = {'exp': np.nan, 'low': np.nan, 'high': np.nan, 'source': None, 'name': x}
            else:
                total_exps += 1
        if (total_exps == len(exps)):
            fh5.write("Hello")
            #fh.write("<p><b>No expression or abundance data available for the given gene.</b></p> ")
            data = None
        else:
            t1 = {k: v for k, v in t1.items() if pd.Series(v).notna().all()}
            t2 = {k: v for k, v in t2.items() if pd.Series(v).notna().all()}
            if len(t1) >= 1:
                myexp1, myexp2 = t1, t2
            # print({k: v for k, v in myexp1.items() if pd.Series(v).notna().all()})
            # print({k: v for k, v in myexp2.items() if pd.Series(v).notna().all()})

            # print(myexp1)
            # print(myexp2)

    if data != None:
        exp_by_source1 = group_by_source(myexp1)
        exp_by_source2 = group_by_source(myexp2)
        fig = plt.figure(figsize=(10, 6), dpi=300)
        fig2 = plt.figure(figsize=(10, 6), dpi=80)
        ax = fig.add_subplot('111')
        ax2 = fig2.add_subplot('111')
        ind = 0
        colors = ['blue', 'red', 'green', 'yellow', 'purple', 'orange', 'cyan', 'pink']
        sources = list(exp_by_source1)
        sources.sort(key=lambda s: len(exp_by_source1[s]))
        sources.reverse()
        legend_colors = []
        legend_text = []
        xlabels = []
        mysource = []
        xhigh = []
        xlow = []
        yhigh = []
        ylow = []
        mydesc = []
        myname2 = []
        myy2 = []
        myx2 = []
        maxy = 0
        maxx = 0
        for sind, s in enumerate(sources):
            no_val_x = []
        no_val_y = []
        myx = []
        myy = []
        myname = []

        for xind, x in enumerate(exp_by_source1[s]):
            myname.append(x['name'])
            if (x['exp'] == 0):
                myx.append(x['exp'])
                no_val_x.append(0)
            elif (x['exp'] == None):
                # x['exp'] = float('Nan')
                no_val_x.append(None)
                x['exp'] = 0
                myx.append(x['exp'])
            elif (x['exp'] < 1):
                myx.append((x['exp']))
                no_val_x.append(0)
            else:
                myx.append(math.log10(x['exp']))
                no_val_x.append(0)

            if (exp_by_source2[s][xind]['exp'] == 0):
                myy.append(exp_by_source2[s][xind]['exp'])
                no_val_y.append(0)
            elif (exp_by_source2[s][xind]['exp'] == None):
                # myy.append(float('Nan'))
                no_val_y.append(None)
                myy.append(0)
            elif (exp_by_source2[s][xind]['exp'] < 1):
                myy.append(exp_by_source2[s][xind]['exp'])
                no_val_y.append(0)
            else:
                myy.append(math.log10(exp_by_source2[s][xind]['exp']))
                no_val_y.append(0)

            mysource.append('<a href=\\x22%s\\x22>%s</a>' % (exp2data[mygene1]['link'], s))
            if (x['high'] == 0):
                xhigh.append(x['high'])
            elif (x['high'] == None):
                # x['exp'] = float('Nan')
                # no_val_x = None
                x['high'] = 0
                xhigh.append(x['high'])
            elif (x['high'] < 1):
                xhigh.append(x['high'])
            else:
                xhigh.append(math.log10(x['high']))
            if (x['low'] == 0):
                xlow.append(x['low'])
            elif (x['low'] == None):
                # x['exp'] = float('Nan')
                # no_val_x = None
                x['low'] = 0
                xlow.append(x['low'])
            elif (x['low'] < 1):
                xlow.append(x['low'])
            else:
                xlow.append(math.log10(x['low']))
            if (exp_by_source2[s][xind]['high'] == 0):
                yhigh.append(exp_by_source2[s][xind]['high'])
            elif (exp_by_source2[s][xind]['high'] == None):
                # myy.append(float('Nan'))
                # no_val_y = None
                yhigh.append(0)
            elif (exp_by_source2[s][xind]['high'] < 1):

                yhigh.append(exp_by_source2[s][xind]['high'])
            else:
                yhigh.append(math.log10(exp_by_source2[s][xind]['high']))

            if (exp_by_source2[s][xind]['low'] == 0):
                ylow.append(exp_by_source2[s][xind]['low'])
            elif (exp_by_source2[s][xind]['low'] == None):
                # myy.append(float('Nan'))
                # no_val_y = None
                ylow.append(0)
            elif (exp_by_source2[s][xind]['low'] < 1):

                ylow.append(exp_by_source2[s][xind]['low'])
            else:
                ylow.append(math.log10(exp_by_source2[s][xind]['low']))
            # print(x)
            mydesc.append(exp2data[mygene1]['desc'])

            if myy[-1] > maxy: maxy = myy[-1]
            if myx[-1] > maxx: maxx = myx[-1]
        # print x['name'],exp_by_source2[s][xind]['name']
        # ax.text(myx[-1], myy[-1], x['name'], fontsize=10)
        # ax2.text(myx[-1], myy[-1], x['name'], fontsize=7)
        if sind < len(colors):
            mycolor = colors[sind]
        else:
            mycolor = 'gray'

        A = np.vstack([myx, np.ones(len(myx))]).T
        m, c = np.linalg.lstsq(A, np.array(myy))[0]

        pearR = np.corrcoef(myx, myy)[1, 0]

        l = [x * int(m + c) for x in myx]

        rects = ax.scatter(myx, myy, color=mycolor)
        rects1 = ax.plot(myx, l, color=mycolor)
        sc = ax2.scatter(myx, myy, color=mycolor)

        if mycolor != 'gray':
            legend_text.append(s + " Fit %6s, r = %6.2e" % (mycolor, pearR))

            legend_colors.append(rects)
            # ind = myind[-1] + 1
            myx2.extend(myx)
            myy2.extend(myy)
            myname2.extend(myname)
        # ax.legend( legend_colors,legend_text,bbox_to_anchor=(1.1,1.1))
        ax.set_xlabel('%s (FPKM)' % Nam1)
        ax.set_ylabel('%s (dNSAF)' % Nam2)
        ax2.set_xlabel('%s (FPKM)' % Nam1)
        ax2.set_ylabel('%s (dNSAF)' % Nam2)

        # ax.set_title('Tissue Specific Expression of %s' % mygene)
        fig.tight_layout()
        fig2.tight_layout()
        if maxx == 0:
            maxx = 1
        if maxy == 0:
            maxy = 1
        if xmax == 0:
            ax.set_xlim(0, maxx * 1.15)
            ax2.set_xlim(0, maxx * 1.15)
        else:
            ax.set_xlim(0, xmax * 1.05)
            ax2.set_xlim(0, xmax * 1.05)
        if ymax == 0:
            ax.set_ylim(0, maxy * 1.15)
            ax2.set_ylim(0, maxy * 1.15)
        else:

            ax.set_ylim(0, ymax * 1.05)
            ax2.set_ylim(0, ymax * 1.05)

        ax.legend(legend_colors, legend_text, loc='upper center', ncol=5)

        ax2.legend(legend_colors, legend_text, loc='upper center', ncol=5)
        fig.savefig(('./tmp/%s-%s-%s.svg' % (Nam1, Nam2, genes)), dpi=300)
        fig.savefig(('./tmp/%s-%s-%s.png' % (Nam1, Nam2, genes)), dpi=300)
        fig2.savefig(('./tmp/%s-%s-%s-front.png' % (Nam1, Nam2, genes)), dpi=80)

        points = ax2.transData.transform(list(zip(myx2, myy2)))
        xcoords, ycoords = [], []
        for p1, p2 in points:
            xcoords.append(p1)
            ycoords.append(p2)

        image_height = fig2.get_figheight() * 80
        image_width = fig2.get_figwidth() * 80

        # changed here
        for n, i in enumerate(no_val_x):
            if i == None:
                myx[n] = float('Nan')
        # changed here

        for n, i in enumerate(no_val_y):
            if i == None:
                myy[n] = float('Nan')

        # # print(fh)
        fh.write('''
            <SCRIPT>
            function mouseover(myname,mysource,mydesc,myx,myy) {
              var expname = document.getElementById("expname");
              expname.innerHTML = myname;
              var papername = document.getElementById("papername");
              papername.innerHTML = mysource;
              var description = document.getElementById("description");
              description.innerHTML = mydesc;
              var myxspan = document.getElementById("myx");
              if(myx == 'nan')
                  {
                    myxspan.innerHTML = "Not available";
                  }
                  else
                  {
                    myxspan.innerHTML = myx;

                  }
                  var myyspan = document.getElementById("myy");
                  if(myy == 'nan')
                  {
                    myyspan.innerHTML = "Not available";
                  }
                  else
                  {
                    myyspan.innerHTML = myy;

                  }
                }


            </SCRIPT>''')

        fh.write(
            '<a href="./tmp/%s-%s-%s.png"><IMG SRC="./tmp/%s-%s-%s.png" ismap usemap="#points" WIDTH="%d" HEIGHT="%d"></a><MAP name="points">' % (
                Nam1, Nam2, genes, Nam1, Nam2, genes, image_width, image_height))

        for xc, yc, experiment, paper, description, gene1exp, gene2exp in zip(xcoords, ycoords, myname2, mysource,
                                                                              mydesc,
                                                                              myx, myy):
            # fh.write( "\n" + experiment + "\n" + paper + "\n" + description +  "\n" + str(gene1exp) + "\n" + str(gene2exp) + "\n" )
            fh.write("""<AREA shape="circle" coords="%s,%s,3" onmouseover="javascript:mouseover('%s');">\n""" % (
                xc, image_height - yc, "','".join([experiment, paper, description, str(gene1exp), str(gene2exp)])))

        fh.write('</MAP>')
        fh.write('''<br><b>Experiment:</b> <SPAN id='expname'></SPAN> <b>Paper:</b> <SPAN id='papername'></SPAN><br><b>Description: </b><SPAN id="description"></SPAN><br>
            ''')
        fh.write("<b>%s:</b> " % Nam1)
        fh.write("""<b></b> <SPAN id='myx'></SPAN> FPKM<br>
                """)
        fh.write("<b>%s:</b> " % Nam2)
        fh.write("""<b></b> <SPAN id='myy'></SPAN> NSAF
                    """)
        fh.close()
    else:
        fh.write("<p><b>No expression or abundance data available for the given gene.</b></p> ")

        fh.close()

else:
    fh.write("<p><b>No expression or abundance data available for the given gene.</b></p> ")

    fh.close()

