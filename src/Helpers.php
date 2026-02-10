<?php
function image($path)
{
    return str_replace(' ', '%20', Storage::disk('external')->url($path));
}