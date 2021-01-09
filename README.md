# codeigniter3-plus
A skeleton project using codeigniter3 + hmvc + module builder + database tablestructure editor
## Motivation / Purpose
Clone this repository to get a skeleton webpage which uses HMVC Pattern on CodeIgniter3.
This skeleton site encloses a module-builder allowing you to create modules in fast manner, which implements basic CRUD 
funcionality on identical named tables in a database. One table for one module with same name.

Idea is to get fast data into an application. So there is more room and time for business logic solutions in our head.

## Installation

* prepare a vhost below your webserver configuration
* create a database with user allowed to create / modifying tables in it
* clone this repository in the document root folder
* correct database settings in file /htdocs/application/config/database.php  (username, password, etc...)
* correct in file /htdocs/application/config/configuration.php the BASE_URL variable
* load up the site using your browser

## Usage

Once the site is up and running, use the two menu entries on the right hand side.
One is used for creation of modules. It simply generates a directory structure below the "modules" folder and inserts some basic
files in it. A basic CRUD implementation is ready to go on it.

Use the second menu item for defining, changing data table structure. Bevore modifying a column, you will need to select one an
then make your changes. However, the skeleton supports also adding data into table... the first datarow needs currently to be put manually in, by a 
database administration tool, or a simple insert statements in the mysql-client. (will be fixed one day :-)    )

## Advanced Usage

Within the created controller file of the newly generated module you have the possibility for configuring how the
displayed table behaves. You can subpress columns, allow deletion, updating or generating new entries.
Beside that, you can rename the displaynames of columns. 



