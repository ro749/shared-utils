<?php
function image($path)
{
    return Storage::disk('external')->url($path);
}