#Retreat Registration for Formidable Forms

## About

A bunch of functions to deal with retreat registration using Formidable Forms for a client's project.


## Testing

To run the tests, 
* install Composer 
* you will need to checkout Wordpress source code for developers and Formidable 

```bash
$ svn co http://develop.svn.wordpress.org/trunk/
```

```bash
$ git clone https://github.com/Strategy11/formidable-forms.git
```
* Make sure to run ```composer install``` on all projects
* change the directory path in **bootstrap.php**
* to execute the tests:

```bash
$ composer exec phpunit
```

