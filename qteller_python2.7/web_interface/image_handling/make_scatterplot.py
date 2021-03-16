import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import numpy as np
import sys
import argparse
import os

db_file = 'qt4db'
width = .7
current_dir = os.path.dirname(os.path.abspath(__file__))
web_interface_dir = '/'.join(current_dir.split('/')[:-1])


class qteller_db:
    def __init__(self,database):
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
            results[row['experiment_id']] = {'link':row['link'],'desc':row['description']}
        return results   
    def gene_exp(self,gene):
        import sqlite3
        results = {}
        self.conn.row_factory = sqlite3.Row
        c = self.conn.cursor()
        c.execute("select * from exp_table where gene=?",(gene,))
        for row in c:
            results[row['experiment_id']] = {'exp':row['exp_val'],'low':row['exp_low'],'high':row['exp_high'],'source':row['source_id'],'name':row['experiment_id']}
        return results

def group_by_source(exps):
    sources = {}
    for e in exps:
        if not exps[e]['source'] in sources: sources[exps[e]['source']] = []
        sources[exps[e]['source']].append(exps[e])
    for x in sources:
        sources[x].sort(key= lambda val: val['name'])
    return sources

#configuring matplotlib
matplotlib.rcParams['xtick.major.size'] = 0
matplotlib.rcParams['xtick.minor.size'] = 0
matplotlib.rcParams['ytick.major.size'] = 0
matplotlib.rcParams['ytick.minor.size'] = 0
#matplotlib.rcParams['lines.linewidth'] = 3
matplotlib.rcParams['patch.linewidth'] = 2
matplotlib.rcParams['axes.linewidth'] = 2
matplotlib.rcParams['legend.fancybox'] = 'True'
matplotlib.rcParams['legend.shadow'] = 'True'
matplotlib.rcParams['legend.fontsize'] = 'small'
matplotlib.rcParams['legend.markerscale'] = .4
#xtick.major.size
#xtick.minor.size
#ytick.major.size
#ytick.minor.size
parser = argparse.ArgumentParser(description="Generate a gene expression bar chart.")
parser.add_argument('--gene1',dest='mygene1',help="The name of the gene to draw on the x axis")
parser.add_argument('--gene2',dest='mygene2',help="The name of the gene to draw on the y axis")
parser.add_argument('--exps',dest='exps',help="A | separated list of the expression conditions to include in the figure.",default='')
parser.add_argument('--image_opts',dest='iopts',help="A | separated list of options to change how the figure is drawn.",default='')
parser.add_argument('--dpi',help='The resolution of the figure to be drawn',default=300,type=int)
parser.add_argument('--ymax',type=int,default=0)
parser.add_argument('--xmax',type=int,default=0)
args = parser.parse_args()
mygene1 = args.mygene1
mygene2 = args.mygene2
exps = args.exps
mydb = qteller_db(db_file)
s2e = mydb.stub2exp()
exp2data = mydb.exp2data()
myexp1 = mydb.gene_exp(mygene1)
myexp2 = mydb.gene_exp(mygene2)
ymax = args.ymax
xmax = args.xmax
if len(exps) > 0:
    include_vals = exps.split('|')
    t1,t2 = {},{}
    for x in include_vals:
    	if x == '': continue
        t1[s2e[x]] = myexp1[s2e[x]]
        t2[s2e[x]] = myexp2[s2e[x]]
    if len(t1) > 1:
	    myexp1,myexp2 = t1,t2
exp_by_source1 = group_by_source(myexp1)
exp_by_source2 = group_by_source(myexp2)
fig = plt.figure(figsize=(10,6),dpi=300)
fig2 = plt.figure(figsize=(10,6),dpi=80)
ax = fig.add_subplot('111')
ax2 = fig2.add_subplot('111')
ind = 0
colors = ['blue','red','green','yellow','purple','orange','cyan','pink']
sources = list(exp_by_source1)
sources.sort(key= lambda s: len(exp_by_source1[s]))
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
for sind,s in enumerate(sources):
    myx = []
    myy = []
    myname = []
    for xind,x in enumerate(exp_by_source1[s]):
    	myname.append(x['name'])
    	myx.append(x['exp'])
    	myy.append(exp_by_source2[s][xind]['exp'])
    	mysource.append('<a href=\\x22%s\\x22>%s</a>' % (exp2data[x['name']]['link'],s))
    	xhigh.append(x['high'])
    	xlow.append(x['low'])
    	yhigh.append(exp_by_source2[s][xind]['high'])
    	ylow.append(exp_by_source2[s][xind]['low'])
    	mydesc.append(exp2data[x['name']]['desc'])
        if myy[-1] > maxy: maxy = myy[-1]
        if myx[-1] > maxx: maxx = myx[-1]
#        print x['name'],exp_by_source2[s][xind]['name']
        ax.text(myx[-1],myy[-1],x['name'],fontsize=10)
        ax2.text(myx[-1],myy[-1],x['name'],fontsize=7)
    if sind < len(colors):
        mycolor = colors[sind]
    else:
        mycolor = 'gray'
#    print myx,myy
    rects = ax.scatter(myx,myy,color=mycolor)
    sc = ax2.scatter(myx,myy,color=mycolor)
    if mycolor != 'gray':
        legend_text.append(s)
        legend_colors.append(rects)
#    ind = myind[-1] + 1
	myx2.extend(myx)
	myy2.extend(myy)
	myname2.extend(myname)
#ax.legend( legend_colors,legend_text,bbox_to_anchor=(1.1,1.1))
ax.set_xlabel('%s Expression (FPKM)' % mygene1)
ax.set_ylabel('%s Expression (FPKM)' % mygene2)
ax2.set_xlabel('%s Expression (FPKM)' % mygene1)
ax2.set_ylabel('%s Expression (FPKM)' % mygene2)
#ax.set_title('Tissue Specific Expression of %s' % mygene)
fig.tight_layout()
fig2.tight_layout()
if xmax == 0:
	ax.set_xlim(0,maxx*1.15)
	ax2.set_xlim(0,maxx*1.15)
else:
	ax.set_xlim(0,xmax*1.05)
	ax2.set_xlim(0,xmax*1.05)
if ymax == 0:
	ax.set_ylim(0,maxy*1.15)
	ax2.set_ylim(0,maxy*1.15)
else:
	print "hi"
	print ymax,xmax,maxy,maxx
	ax.set_ylim(0,ymax*1.05)
	ax2.set_ylim(0,ymax*1.05)
print ax2.set_xlim()

ax.legend(legend_colors,legend_text,loc='upper center',ncol=5)
ax2.legend(legend_colors,legend_text,loc='upper center',ncol=5)
fig.savefig(('./tmp/%s-%s.svg' % (mygene1,mygene2)),dpi=300)
fig.savefig(('./tmp/%s-%s.png' % (mygene1,mygene2)),dpi=300)
fig2.savefig(('./tmp/%s-%s-front.png' % (mygene1,mygene2)),dpi=80)
points = ax2.transData.transform(zip(myx2,myy2))
xcoords,ycoords = [],[]
for p1,p2 in points:
	xcoords.append(p1)
	ycoords.append(p2)
image_height = fig2.get_figheight()*80
image_width = fig2.get_figwidth()*80
fh = open(('./tmp/%s-%s-map.html' % (mygene1,mygene2)),'w')
fh.write('''
<SCRIPT>
function mouseover(myname,mysource,mydesc,myx,xlow,xhigh,myy,ylow,yhigh) {
  var expname = document.getElementById("expname");
  expname.innerHTML = myname;
  var papername = document.getElementById("papername");
  papername.innerHTML = mysource;
  var description = document.getElementById("description");
  description.innerHTML = mydesc;
  var myxspan = document.getElementById("myx");
  myxspan.innerHTML = myx;
  var myyspan = document.getElementById("myy");
  myyspan.innerHTML = myy;
  var xlowspan = document.getElementById("xlow");
  xlowspan.innerHTML = xlow;
  var ylowspan = document.getElementById("ylow");
  ylowspan.innerHTML = ylow;
  var xhighspan = document.getElementById("xhigh");
  xhighspan.innerHTML = xhigh;
  var yhighspan = document.getElementById("yhigh");
  yhighspan.innerHTML = yhigh;
}

</SCRIPT>''')
fh.write('<a href="./tmp/%s-%s.png"><IMG SRC="./tmp/%s-%s-front.png" ismap usemap="#points" WIDTH="%d" HEIGHT="%d"></a><MAP name="points">' % (mygene1,mygene2,mygene1,mygene2,image_width,image_height))

for xc,yc,experiment,paper,description,gene1exp,gene1low,gene1high,gene2exp,gene2low,gene2high in zip(xcoords,ycoords,myname2,mysource,mydesc,myx2,xlow,xhigh,myy2,ylow,yhigh):
	fh.write("""<AREA shape="circle" coords="%d,%d,3" onmouseover="javascript:mouseover('%s');">\n""" % (xc,image_height-yc,"','".join([experiment,paper,description,str(gene1exp),str(gene1low),str(gene1high),str(gene2exp),str(gene2low),str(gene2high)])))
fh.write('</MAP>')
fh.write('''<br><b>Experiment:</b> <SPAN id='expname'></SPAN> <b>Paper:</b> <SPAN id='papername'></SPAN><br><b>Description: </b><SPAN id="description"></SPAN><br>
''')
fh.write("<b>%s:</b> " % mygene1)
fh.write("""<b>Expression</b> <SPAN id='myx'></SPAN> FPKM <b>Lower Limit:</b> <SPAN id='xlow'></SPAN> FPKM <b>Upper Limit:</b> <SPAN id='xhigh'></SPAN> FPKM<br>
""")
fh.write("<b>%s</b> " % mygene2)
fh.write("""<b>Expression:</b> <SPAN id='myy'></SPAN> FPKM <b>Lower Limit:</b> <SPAN id='ylow'></SPAN> FPKM <b>Upper Limit:</b> <SPAN id='yhigh'></SPAN> FPKM
""")
fh.close()
