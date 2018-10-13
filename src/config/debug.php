<?php

declare(strict_types=1);

function dump($data)
{
    echo '<pre>';
    var_dump($data);
}

function prn($data)
{
    dump($data);
}

function prnx($data)
{
    dump($data);
    die();
}