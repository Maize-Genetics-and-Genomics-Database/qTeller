import pandas as pd
import numpy as np

#input metadata file
file = 'metadata.csv'

#Take the input metadata file and convert every expression values that are empty or NA to float Nan
#change the seperator as per the meta data file seperator

rawfile = pd.read_csv(file, sep=',', na_values=["NA","","None"])
# print(rawfile['Endosperm 12 DAP'].head())

#Save the metadata file after Nan float conversion with the same name
rawfile.to_csv(file,na_rep = np.NaN, sep=',',index=None)
