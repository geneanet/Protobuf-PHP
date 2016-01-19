<?php
/*
    Receives the following variables:
        $namespace - The current namespace (aka package)
        $data      - An array of google.protobuf.FieldDescriptorProto objects
        $nbSpaces  - The number of spaces
*/

use DrSlump\Protobuf\Protobuf;
use RuntimeException;

$spaces = str_repeat(' ', $nbSpaces);
echo 'use DrSlump\Protobuf\Field;'.PHP_EOL;
echo 'use DrSlump\Protobuf\Protobuf;'.PHP_EOL;
echo PHP_EOL;

foreach ($data as $f) {
    echo '\\'.$this->ns($f->extendee).'::extension(function()'.PHP_EOL;
    echo '{'.PHP_EOL;
    echo $spaces.'// '.$this->rule($f).' '.$this->type($f).' '.$f->name.' = '.$f->number.PHP_EOL;
    echo $spaces.'$f = new Field();'.PHP_EOL;
    echo $spaces.'$f->number = '.$f->number.';'.PHP_EOL;
    echo $spaces.'$f->name = \''.$f->name.'\';'.PHP_EOL;
    echo $spaces.'$f->rule = Protobuf::RULE_'.strtoupper($this->rule($f)).';'.PHP_EOL;
    echo $spaces.'$f->type = Protobuf::TYPE_'.strtoupper($this->type($f)).';'.PHP_EOL;

    if ($f->hasTypeName()) {
        $ref = $f->type_name;
        if (substr($ref, 0, 1) !== '.') {
            throw new RuntimeException("Only fully qualified names are supported but found '$ref' at $ns");
        }
        echo $spaces.'$f->reference = \''.$this->ns($ref).'\';'.PHP_EOL;
    }

    if ($f->hasDefaultValue()) {
        switch ($f->type) {
            case Protobuf::TYPE_BOOL:
                $bool = filter_var($f->default_value, FILTER_VALIDATE_BOOLEAN);
                echo $spaces.'$f->default = '.($bool ? 'true' : 'false').';'.PHP_EOL;
                break;
            case Protobuf::TYPE_STRING:
                echo $spaces.'$f->default = \''.addcslashes($f->default_value, '\'\\').'\';'.PHP_EOL;
                break;
            case Protobuf::TYPE_ENUM:
                echo $spaces.'$f->default = \\'.$this->ns($f->type_name).'::'.$f->default_value.';'.PHP_EOL;
                break;
            default: // Numbers
                echo $spaces.'$f->default = '.$f->default_value.';'.PHP_EOL;
        } // switch
    }

    echo $spaces.'// @@protoc_insertion_point(scope_extension)'.PHP_EOL;
    echo $spaces.'// @@protoc_insertion_point(extension_'.$namespace.':'.$f->name.')'.PHP_EOL;
    echo PHP_EOL;
    echo $spaces.'return $f;'.PHP_EOL;
    echo '});'.PHP_EOL;
}
