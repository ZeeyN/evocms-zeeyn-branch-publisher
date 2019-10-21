<?php

namespace EvolutionCMS\ZeeyN;

use EvolutionCMS\Models\SiteContent;
use Event;

function rootClimber($data, string $published = '0') {
    /*
     * incoming types:
     * array,
     * string,
     * integer,
     * Eloquent model
     */
    switch (gettype($data)) {
        case 'integer':
            $object = SiteContent::where('published', $published)->where('id', $data)->first();
            break;
        case 'object':
            $object = $data;
            break;
        case 'array':
            $object = SiteContent::where('published', $published)->where('id', array_shift($data))->first();
            break;
        case 'string':
            $data = intval($data);
            $object = SiteContent::where('published', $published)->where('id', $data)->first();
            break;
        case 'boolean':
        case 'double':
        case 'resource':
        case 'resource (closed)':
        case 'NULL':
        case 'unknown type':
            return false;
            break;
    }
    $document = SiteContent::where('id', $object->id)->first();
    $document->published = $published;
    $document->save();
    if ($document->isfolder == '1') {
        switch ($published) {
            case '0':
                $childs = SiteContent::where('published', '1')->where('parent', $document->id)->get();
                break;
            case '1':
                $childs = SiteContent::where('published', '0')->where('parent', $document->id)->get();
                break;
        }
        foreach ($childs as $child) {
            rootClimber($child, $published);
        }
    } else {
        return true;
    }
}



Event::listen('evolution.OnDocPublished', function($data){
    rootClimber($data['docid'], '1');
});
Event::listen('evolution.OnDocUnPublished', function($data){
    rootClimber($data['docid'], '0');
});

