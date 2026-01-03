<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends MainAdminController
{
    public function __construct()
    {
        $this->middleware('auth');
        parent::__construct();
        check_verify_purchase();
    }

    public function index()
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = "Announcements";
        $announcements = Announcement::orderBy('id', 'desc')->get();

        return view('admin.pages.announcements.list', compact('page_title', 'announcements'));
    }

    public function add()
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = "Add Announcement";

        return view('admin.pages.announcements.addedit', compact('page_title'));
    }

    public function edit($id)
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = "Edit Announcement";
        $announcement = Announcement::findOrFail($id);

        return view('admin.pages.announcements.addedit', compact('page_title', 'announcement'));
    }

    public function addnew(Request $request)
    {
        $data = \Request::except(['_token']);
        $inputs = $request->all();

        $rule = [
            'title' => 'required',
            'message' => 'required'
        ];

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

        if (!empty($inputs['id'])) {
            $announcement = Announcement::findOrFail($inputs['id']);
        } else {
            $announcement = new Announcement;
        }

        $announcement->title = $inputs['title'];
        $announcement->message = $inputs['message'];
        $announcement->is_active = isset($inputs['is_active']) ? 1 : 0;
        $announcement->show_as_popup = isset($inputs['show_as_popup']) ? 1 : 0;

        $announcement->save();

        if (!empty($inputs['id'])) {
            \Session::flash('flash_message', 'Announcement updated successfully');
            return redirect('admin/announcements');
        } else {
            \Session::flash('flash_message', 'Announcement added successfully');
            return redirect('admin/announcements');
        }
    }

    public function delete($id)
    {
        if(Auth::User()->usertype!="Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        Announcement::findOrFail($id)->delete();

        \Session::flash('flash_message', 'Announcement deleted successfully');
        return redirect()->back();
    }
}
