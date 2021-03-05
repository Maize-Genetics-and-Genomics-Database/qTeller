qTeller
=======

RNA-seq processing pipeline and modular web interface

For an examples of MaizeGDB's qTeller web interfaces visit 
<a href="http://qteller2.usda.iastate.edu">http://qteller2.usda.iastate.edu</a>

## Overview ##

| Directory Name   | Description |
|------------------|:---------------------:|
| [build_db](/backup_of_build_db_web_interface/build_db) | Scripts for constructing the SQLite DB|
| [rna_process](/rna_process) | RNA analysis scripts|
| [web_interface](/backup_of_build_db_web_interface/web_interface) | Public facing files that are served by the Apache Server.|


## Virtual Machine ##

The MaizeGDB qTeller VM (qteller2) can be found under MaizeGDB's vSphere resource pool "Dev" within datacenter blade3.usda.iastate.edu:

```
blade3.usda.iastate.edu
└── Dev
    └── qteller2
```

### VM Specs

```
Hostname: qteller2.usda.iastate.edu
Operating System: CentOS release 6.9 (Final)
VM Version: 8
Memory: 4096 MB
Memory Overhead: 44.05 MB
CPU: 1 vCPU
```

### Access
For username / password details, please contact [Carson Andorf](mailto:carson.andorf@ars.usda.gov).


## Environment Requirements ##

#### PHP Version 5.3.3
* No additional libraries required.

#### Python Version 2.7.13
* See [requirements.txt](requirements.txt) and [python modules](python_modules.txt) for a list of dependencies.
* **NOTE**: The host VM has Python 2.6.6 installed by default, and this should not be removed! See [Python](#python) for details.

#### Apache Web Server Version 2.2.15
* No untypical customization is needed.


## Installation ##

### Apache / PHP
See [an example](https://support.rackspace.com/how-to/centos-6-apache-and-php-install/) of installing Apache on CentOS 6.
* For an example of a working `httpd.conf` file, please consult `/etc/httpd/conf/httpd.conf` on the [VM](#virtual-machine).

### Python

Centos 6.* comes with Python 2.6, but we can't just replace it with v2.7 because it's used by the OS internally so you will need to install v2.7 along with it.  Fortunately, CentOS made this quite painless with their [Software Collections Repository](http://wiki.centos.org/AdditionalResources/Repositories/SCL).
```
$ sudo yum update # update yum
$ sudo yum install centos-release-scl # install SCL 
$ sudo yum install python27 # install Python 2.7
``` 

To use it, you essentially spawn another shell (or script) while enabling the newer version of Python:

```
$ scl enable python27 bash
```

Alternatively, you can run commands directly like this:

```
$ scl enable python27 'python --version' # calls Python 2.7
```

To install [additional libraries](requirements.txt), you will need to install PIP:

```
$ yum install python27-python-pip
```

once configured, you can install PIP modules into Python2.7 like this:

```
$ scl enable python27 'pip install -r requirements.txt'
```

**NOTE**: if your username doesn't require root to install software, then `LD_LIBRARY_PATH` and `PATH` is set up for you automatically by `scl`.  Also keep in mind that using SCL outside a shell (e.g., cronjobs) [isn't quite straightforward](http://stackoverflow.com/questions/16631461/scl-enable-python27-bash).  Also, using `virtualenv` poses a challenge as well.


### Additional Instructions

Upon successful installation of Python, PHP, and Apache, you can `git clone` this project into your Apache directory. The public-facing directories are located in the [web_interface](/backup_of_build_db_web_interface/web_interface) directory. Assuming a default Apache installation, the DocumentRoot in the `httpd.conf` would look like this:

```
DocumentRoot "/var/www/html/qTeller/web_interface"
```

See [Adding new data](#adding-new-data) on final steps for generating the DB.

## Adding new data ##

The qTeller database generation script requires the following 3 files:
  1. FPKM_TRACKING files
      * If it doesn't exist already, create the **build_db/fpkm_tracking** directory:
      `$ mkdir build_db/fpkm_tracking`
      * Drop your fpkm_tracking files in the **build_db/fpkm_tracking** directory. They must end with the **.fpkm_tracking** file extension.
  2. GFF file
      * Download a GFF file for your desired genome and place it in the [build_db](/backup_of_build_db_web_interface/build_db) directory.
  3. CSV file
      * Create a metadata file in CSV format so the script knows how to interpret the fpkm_tracking files. Here is an [example](backup_of_build_db_web_interface/build_db/anno_meta_maizev4.csv).
      * **NOTE:** The *File_handle* column specifies the name of the fpkm tracking file to load **(minus the .fpkm_tracking file extension)**
    
Assuming you have the required files, you can create the SQLite DB using the following command: 

```
$ cd build_db
$ scl enable python27 'python build_qt_db.py <METADATA.CSV> --gff_file <GFF.gff3> --info_dir ./fpkm_tracking' # creates qt4db
```

where <METADATA.CSV> is the CSV file (3), <GFF.gff3> is the GFF file (2), and fpkm_tracking is the directory where the FPKM_TRACKING files are kept (1) as described above. This will create a `qt4db` SQLite file.

* *Fun fact:* you can use the [SQLite Viewer](http://inloop.github.io/sqlite-viewer/) to easily look inside the DB and experiment with queries.

Finally, the last step is to move the generated `qt4db` file into the [web_interface](/backup_of_build_db_web_interface/web_interface) directory:

```
$ mv qt4db ../web_interface/
```

You should now be able to access qTeller through your browser.
