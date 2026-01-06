<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'message' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'cta_text' => 'nullable|string|max:100',
            'cta_url' => 'nullable|url|max:255',
            'cta_target' => 'nullable|in:_self,_blank'
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
        $announcement->is_active = (int)($inputs['is_active'] ?? 0);
        $announcement->show_as_popup = (int)($inputs['show_as_popup'] ?? 0);
        $announcement->cta_text = $inputs['cta_text'] ?? null;
        $announcement->cta_url = $inputs['cta_url'] ?? null;
        $announcement->cta_target = $inputs['cta_target'] ?? '_self';

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $upload_path = base_path('public/uploads/announcements');
            if (!file_exists($upload_path)) {
                @mkdir($upload_path, 0775, true);
            }
            $filename = 'ann_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move($upload_path, $filename);
            $announcement->image = 'public/uploads/announcements/' . $filename;
        }

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
