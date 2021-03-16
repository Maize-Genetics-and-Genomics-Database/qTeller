mychr = '1'
start = 1000000
stop = 10000000

import argparse
parser = argparse.ArgumentParser()
parser.add_argument('syn_file',help="The output of SynMap downloaded from the XXX link.")
args = parser.parse_args()
fh = open(args.syn_file)
for x in fh:
    if x[0] == '#': continue
    y = x.strip().split()
    z1 = y[3]
    z2 = y[7]
    z1list = z1.split('||')
    z2list = z2.split('||')
    if z1list[0] == mychr and int(z1list[2]) > start and int(z1list[1]) < stop:
        print "{0},{1}||{2}||{3},{4},{5}||{6}||{7}".format(z1list[3],z1list[0],z1list[1],z1list[2],z2list[3],z2list[0],z2list[1],z2list[2])
    if z2list[0] == mychr and int(z2list[2]) > start and int(z2list[1]) < stop:
        print "{0},{1}||{2}||{3},{4},{5}||{6}||{7}".format(z2list[3],z2list[0],z2list[1],z2list[2],z1list[3],z1list[0],z1list[1],z1list[2])
