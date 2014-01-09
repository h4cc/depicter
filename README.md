[![Build Status](https://travis-ci.org/h4cc/depicter.png?branch=master)](https://travis-ci.org/h4cc/depicter)

# depicter

A aggregating PHP continuous integration tool.


## Usage

Example usage:

### Check Limits

With defaults max "100" comments in file "scrutinizer.json".

```
$ php bin/depicter check:limits
+-------------------------------------------------------+-------+
| Violation                                             | Count |
+-------------------------------------------------------+-------+
| php_md.unused_local_variable                          | 21    |
| php_md.coupling_between_objects                       | 4     |
| php_md.unused_private_method                          | 2     |
| Generic.CodeAnalysis.EmptyStatement.NotAllowedWarning | 2     |
| Generic.CodeAnalysis.UselessOverridingMethod.Found    | 1     |
| php_hhvm.too_few_argument                             | 1     |
+-------------------------------------------------------+-------+
Limit not reached.
```

```
$ php bin/depicter check:limits 3 scrutinizer.json
+-------------------------------------------------------+-------+
| Violation                                             | Count |
+-------------------------------------------------------+-------+
| php_md.unused_local_variable                          | 21    |
| php_md.coupling_between_objects                       | 4     |
| php_md.unused_private_method                          | 2     |
| Generic.CodeAnalysis.EmptyStatement.NotAllowedWarning | 2     |
| Generic.CodeAnalysis.UselessOverridingMethod.Found    | 1     |
| php_hhvm.too_few_argument                             | 1     |
+-------------------------------------------------------+-------+
Limit reached. Too much violations found
Found 31 violations for a limit of 3
```

Without output:

```
$ php bin/depicter check:limits 3 scrutinizer.json --quiet || echo Failed
Failed
```

### Generate a HTML Report

Write a HTML Report to "report/" using the Source Code from "example_code/" and comments from "scrutinizer.json".

```
$ php bin/depicter report:generate report/ example_code/ scrutinizer.json
Depicter report generated from 'example_code/' to 'report/'
```


