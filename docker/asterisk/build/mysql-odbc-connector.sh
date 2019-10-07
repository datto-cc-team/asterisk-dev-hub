# Download a recent MySQL ODBC connector from MySQL’s website
wget https://dev.mysql.com/get/Downloads/Connector-ODBC/8.0/mysql-connector-odbc-8.0.17-linux-ubuntu18.04-x86-64bit.tar.gz

# Unpack MySQL connector tarball and install
# https://dev.mysql.com/doc/connector-odbc/en/connector-odbc-installation-binary-unix-tarball.html

https://dev.mysql.com/get/Downloads/Connector-ODBC/8.0/mysql-connector-odbc-8.0.17-linux-ubuntu18.04-x86-64bit.tar.gz

tar xvf mysql-connector-odbc-8.0.17-linux-ubuntu18.04-x86-64bit.tar.gz
cd mysql-connector-odbc-8.0.17-linux-ubuntu18.04-x86-64bit
cp bin/* /usr/local/bin
cp lib/* /usr/local/lib
myodbc-installer -a -d -n "MySQL ODBC 8.0 Driver" -t "Driver=/usr/local/lib/libmyodbc8w.so"
myodbc-installer -a -d -n "MySQL ODBC 8.0" -t "Driver=/usr/local/lib/libmyodbc8a.so"
