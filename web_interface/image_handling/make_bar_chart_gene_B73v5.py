import matplotlib

matplotlib.use('Agg')
import matplotlib.pyplot as plt
import numpy as np
import sys
import argparse

db_file = '/var/www/html/qTeller/web_interface/gene_protein_qt5db'
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
        c.execute("select * from gene_exp_table where gene=?", (gene,))
        for row in c:
            results[row['experiment_id']] = {'exp': row['exp_val'], 'low': row['exp_val'], 'high': row['exp_val'],
                                             'source': row['source_id'], 'name': row['experiment_id']}
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
matplotlib.rcParams['legend.fontsize'] = '6'
matplotlib.rcParams['legend.markerscale'] = .4
matplotlib.rcParams['xtick.labelsize'] = 'xx-small'
# xtick.major.size
# xtick.minor.size
# ytick.major.size
# ytick.minor.size
parser = argparse.ArgumentParser(description="Generate a gene expression bar chart.")
parser.add_argument('--gene', dest='mygene', help="The name of the gene to draw the figure for")
parser.add_argument('--exps', dest='exps',
                    help="A | separated list of the expression conditions to include in the figure.", default='')
parser.add_argument('--image_opts', dest='iopts',
                    help="A | separated list of options to change how the figure is drawn.", default='')
parser.add_argument('--dpi', help='The resolution of the figure to be drawn', default=300, type=int)
parser.add_argument('--ymax', type=int, default=0)
parser.add_argument('--xmax', type=int, default=0)
args = parser.parse_args()
mygene = args.mygene
exps = args.exps
if (len(exps)) == 0:
    expression = 'all'
else:
    expression = str(exps).replace('|', '_')

if(len(expression)> 150):
    mid = int(len(expression)/2)
    expression = expression[mid:(mid+150)]
mydb = qteller_db(db_file)
exp2data = mydb.exp2data()
s2e = mydb.stub2exp()
myexp = mydb.gene_exp(mygene)
genome = 'rna'
fh = open('./tmp/%s-%s-%s-map.html' % (mygene,expression,genome) ,'w')
if (bool(myexp) == True):

    include_vals = args.exps.split('|')
    iopts = set(args.iopts.split('|'))
    if len(include_vals) >= 1:
        myexp2 = {}
        for x in include_vals:
            if x == '': continue
            myexp2[s2e[x]] = myexp[s2e[x]]
        if len(myexp2) >= 1:
            myexp = myexp2
    exp_count = len(myexp)
    upper_limit = .2 * exp_count
    if upper_limit < 11:
        matplotlib.rcParams['xtick.labelsize'] = 'small'
        upper_limit = .4 * exp_count
        if upper_limit < 11:
            upper_limit = .6 * exp_count
            matplotlib.rcParams['xtick.labelsize'] = 'medium'
            if upper_limit < 8:
                upper_limit = 8
    exp_by_source = group_by_source(myexp)
    # print upper_limit
    fig = plt.figure(figsize=(upper_limit, 5), dpi=int(900 / upper_limit))
    ax = fig.add_subplot(111)
    ind = 0
    colors = ['blue', 'red', 'purple', 'yellow', 'green', 'orange', 'cyan', 'pink']
    sources = list(exp_by_source)
    sources.sort(key=lambda s: len(exp_by_source[s]))
    sources.reverse()
    legend_colors = []
    legend_text = []
    xlabels = []
    maxexp = 0
    rect_coords = []
    xhigh = []
    xlow = []
    myname2 = []
    mysource = []
    mydesc = []
    myx = []
    temp_coords = []
    res = int(900 / upper_limit)
    y_height = res * 5
    for sind, s in enumerate(sources):
        myind = []
        for x in range(ind, ind + len(exp_by_source[s])):
            myind.append(x + (1 - width) / 2)
            ind = x + 1
        error_up = []
        error_down = []
        vals = []
        data_point = exp_by_source[s]
        for d in data_point:
            mysource.append('<a href=\\x22%s\\x22>%s</a>' % (exp2data[d['name']]['link'], s))
            mydesc.append(exp2data[d['name']]['desc'])
            if (d['exp'] == None):
                d['exp'] = float('Nan')
                d['high'] = float('Nan')
                d['low'] = float('Nan')
            error_up.append(d['exp'] - d['low'])
            error_down.append(d['high'] - d['exp'])
            xlow.append(d['low'])
            xhigh.append(d['high'])
            if d['high'] > maxexp: maxexp = d['high']
            #        error_down.append(100)
            vals.append(d['exp'])
            xlabels.append(d['name'])
        if sind < len(colors):
            mycolor = colors[sind]
        else:
            mycolor = 'gray'
        error_array = np.array([error_up, error_down])
        rects = ax.bar(myind, vals, width, color=mycolor, yerr=error_array, ecolor='black', capsize=2)
        for i, v in zip(myind, vals):
            temp_coords.append([[i, v], [i + width, 0]])
        if mycolor != 'gray':
            legend_text.append(s)
            legend_colors.append(rects[0])
        myx.extend(vals)
    #    ind = myind[-1] + 1
    # ax.legend( legend_colors,legend_text,bbox_to_anchor=(1.1,1.1))
    ax.set_ylabel('%s Expression (FPKM)' % mygene)
    ax.set_xlim([0, ind])
    if (maxexp == 0):
        ax.set_ylim([0, 1 * 1.25])
    else:
        ax.set_ylim([0, maxexp * 1.25])
    # ax.set_title('Tissue Specific Expression of %s' % mygene)
    xinds = []
    for x in range(ind):
        xinds.append(x + .3)
    ax.set_xticks(xinds)
    ax.set_xticklabels(xlabels, rotation=330, ha='left')
    fig.tight_layout()
    ax.legend(legend_colors, legend_text, loc='upper center', ncol=3, shadow=True, frameon=True)
    fig.savefig(('./tmp/%s-%s-%s-small.png' % (mygene, expression, genome)), dpi=int(900 / upper_limit))
    fig.savefig(('./tmp/%s-%s-%s.svg' % (mygene, expression, genome)), dpi=args.dpi)
    fig.savefig(('./tmp/%s-%s-%s.png' % (mygene, expression, genome)), dpi=args.dpi)
    for t in temp_coords:
        v = ax.transData.transform(t)
        rect_coords.append(','.join([str(v[0][0]), str((5 * res) - v[0][1]), str(v[1][0]), str((5 * res) - v[1][1])]))
    fh.write('''
	<SCRIPT>
	function mouseover(myname,mysource,mydesc,myx,xlow,xhigh) {
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
	  var xlowspan = document.getElementById("xlow");
	  xlowspan.innerHTML = xlow;
	  var xhighspan = document.getElementById("xhigh");
	  xhighspan.innerHTML = xhigh;
	}

	</SCRIPT>''')
    fh.write('<a href="./tmp/%s-%s-%s.png"><IMG SRC="./tmp/%s-%s-%s-small.png" ismap usemap="#points"></a><MAP name="points">' %
    (mygene, expression, genome, mygene, expression, genome))
    for rc, experiment, paper, description, gene1exp, gene1low, gene1high in zip(rect_coords, xlabels, mysource, mydesc,
                                                                                 myx, xlow, xhigh):
        fh.write("""<AREA shape="rect" coords="%s" onmouseover="javascript:mouseover('%s');">\n""" % (
            rc, "','".join([experiment, paper, description, str(gene1exp), str(gene1low), str(gene1high)])))
    fh.write('</MAP>')
    fh.write('''
	<br><b>Experiment:</b> <SPAN id='expname'></SPAN> <b>Paper:</b> <SPAN id='papername'></SPAN><br><b>Description:</b> <SPAN id="description"></SPAN><br>
	''')
    fh.write("<b>%s</b> " % mygene)
    if (d['exp'] == float('Nan')):
        fh.write("""<b>Expression:</b> <SPAN id='myx'></SPAN> or No FPKM
                """)
    else:

        fh.write("""<b>Expression:</b> <SPAN id='myx'></SPAN> FPKM
        """)
    fh.close()

else:

    fh.write("""<b>Expression data is not avaliable for given gene.</b> """)

    fh.close()

