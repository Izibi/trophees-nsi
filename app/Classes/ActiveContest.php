<?php

namespace App\Classes;

use App\Models\Contest;

class ActiveContest {


    public function get() {
        return Contest::where('status', 'open')->first();
    }


    public function set($id) {
        $old = $this->get();
        if($old) {
            if($old->id == $id) {
                return;
            }
            //$old->active = false;
            //$old->save();
        }
        //Contest::where('id', $id)->update(['open' => true]);
    }
}