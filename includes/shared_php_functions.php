<?php
/**
 * Created by PhpStorm.
 * User: cellardoor
 * Date: 27/12/14
 * Time: 12:59
 */
function dropdown_menu($name, array $values, array $options, $selected=null){
    $dropdown = '<select name="'.$name.'" id="'.$name.'">'."\n";

    #foreach($options as $key=>$option){
    foreach (array_combine($values, $options) as $id=>$value){

        $select = $selected==$value ? ' selected' : null;

        $dropdown .= '<option value="'.$id.'"'.$select.'>'.$value.'</option>'."\n";

    }
    $dropdown .= '</select>'."\n";

    return $dropdown;
}

?>