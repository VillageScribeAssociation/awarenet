cat `fgrep .js sketchpad.html  | fgrep '<script ' | cut -f 2 -d '"'` > all.js

