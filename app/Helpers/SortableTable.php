<?php
namespace App\Helpers;

class SortableTable {


    public static function th($field, $title) {
        $url = request()->url();
        $sort = request()->get('sort', [
            'field' => null,
            'order' => null
        ]);
        if($field === $sort['field']) {
            $icon = $sort['order'] == 'desc' ? '<i class="icon-up"/>' : '<i class="icon-down"/>'; 
            $params = [
                'sort[field]' => $field,
                'sort[order]' => $sort['order'] == 'desc' ? 'asc' : 'desc'
            ];
        } else {
            $icon = '';
            $params = [
                'sort[field]' => $field,
                'sort[order]' => 'asc'
            ];
        }
        
        $params = '?'.http_build_query($params);
        return '<a href="'.$url.$params.'">'.$title.$icon.'</a>';
    }


    public static function orderBy(&$query, $sort_fields) {
        if(request()->has('sort')) {
            $sort = request()->get('sort');
            if(isset($sort['field']) && isset($sort['order'])) {
                $field = $sort['field'];
                if(isset($sort_fields[$field])) {
                    $query->orderBy($sort_fields[$field], $sort['order']);
                }
            }
        }        
    }

}