<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter4.github.io/CodeIgniter4/
 */

 function getStar()
 {
    return number_format(mt_rand(40,50)/10, 1);
 }

function sortName()
{
   return [
      1 => '一',
      2 => '二',
      3 => '三',
      4 => '四',
      5 => '五',
      6 => '六',
      7 => '七',
      8 => '八',
      9 => '九',
      10 => '十',
   ];
}
function getSortName($id)
{
   $info = sortName();
   if(isset($info[$id])) {
      return $info[$id];
   } else {
      return '';
   }
}

/**
 * 获取id
 *
 * @param string $url
 * @param string $suffix
 * @return int
 */
function getIdByUrl(string $url, string $suffix = 'html')
{
    preg_match("/([0-9]+)\.{$suffix}/", $url, $idMatchArray);
    if (!empty($idMatchArray)) {
        return (int)$idMatchArray[1];
    }
    return 0;
}

function prx($param){
    echo '<pre>';
    var_dump($param);
    echo '</pre>';
    exit;
}
function getPackUnit($size, $type)

{

    switch($type) {

        case "1":

            //M

            $size = ($size * 1024 * 1024);

            break;

        case "2":

            //G

            $size = ($size * 1024 * 1024 * 1024);

            break;

        case "3":

            //K

            $size = ($size * 1024);

            break;

        default:

            break;

    }

    return $size;

}

