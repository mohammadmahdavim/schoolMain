<?php


namespace App\Http\Controllers\admin;

use App\clas;
use App\FirstMessage;
use App\Home;
use App\HomeImage;
use App\Http\Controllers\Controller;
use App\Job;
use App\KarnamehAdmin;
use App\MainPage;
use App\MainPagee;
use App\Models\Gateway;
use App\Models\Payment;
use App\Moshaver;
use App\OnlineClass;
use App\PreRegistration;
use App\Setting;
use App\User;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use JoisarJignesh\Bigbluebutton\Facades\Bigbluebutton;
use Morilog\Jalali\Jalalian;
use PhpParser\Node\Expr\New_;
use App\Day;


class AdminController extends Controller
{

    /**
     * ImageController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        $payments = Payment::all();
        foreach ($payments as $payment) {
        }
        $user = User::where('id', auth()->user()->id)->pluck('role')->first();
        if ($user == 'دانش آموز' or $user == 'اولیا') {
            return back();
        }
        $modal = FirstMessage::where('receiver', 'بقیه اعضا')->where('modal', 1)->first();
        $messages = FirstMessage::where('receiver', 'بقیه اعضا')->where('modal', 0)->get();
        $day = Jalalian::now()->getDay();
        if ($day < 10) {
            $day = '0' . $day;
        }
        $mounth = Jalalian::now()->getMonth();
        if ($mounth < 10) {
            $mounth = '0' . $mounth;
        }
        $year = Jalalian::now()->getYear();
        $date = $year . '-' . $mounth . '-' . $day;
        $enddate = $year . '/' . $mounth . '/' . $day;
         $day = Jalalian::forge('today')->format('%A'); 
		$day=Day::where('name',$day)->first();
        $onlines = OnlineClass::where('date', '<=', $date)->where('enddate', '>=', $enddate)->where('status', 1)
                                    ->where('day_id',$day->id)

            ->orderby('start')
            ->with('author_class')
            ->get();
        $meetings = Moshaver::where('date', $enddate)
            ->where('user_id', auth()->user()->id)
            ->orderBy('start', 'asc')
            ->get();
        return view('Admin.index', compact('modal', 'messages', 'onlines', 'meetings'));
    }

    public function job(Request $request)
    {
        //        اعتبار سنجی اطلاعات ارسالی از فرم

        $this->validate(request(), [
            'job' => 'required',
        ]);

//ایجاد ردیف جدید در جدول jobs
        $user_id = auth()->user()->id;
        $id = Job::create([
            'job' => request('job'),
            'user_id' => auth()->user()->id,
            'created_at' => Jalalian::now(),
            'updated_at' => Jalalian::now(),
        ]);

        return redirect('Admin/home');

    }

    public function changeStatus(Request $request)
    {
        $RTamas = RTamas::find($request->id);
        $RTamas->status = $request->status;
        $RTamas->save();

        return response()->json(['success' => 'Status change successfully.']);
    }

    public function mainpage()
    {

        $rows = DB::table('main_pages')->first();
        $rowss = DB::table('main_pagees')->whereNotIn('id', [1])->get();
        $rowsss = MainPagee::where('id', 1)->first();

        return view('Admin.mainpage', compact('rows', 'rowss', 'rowsss'));
    }

    public function mainpagestore(Request $request)
    {

        $row = MainPage::where('id', 1)->first();
        if (!empty($row)) {
            $row->update([
                'phone' => request('phone'),
                'email' => request('email'),
                'day' => request('day'),
                'time' => $request->time,
                'updated_at' => Jalalian::now(),
            ]);
        } else {
            MainPage::create([
                'phone' => request('phone'),
                'email' => request('email'),
                'day' => request('day'),
                'time' => $request->time,
                'created_at' => Jalalian::now(),
                'updated_at' => Jalalian::now(),
            ]);
        }
        $rows = DB::table('main_pages')->first();
        $rowss = DB::table('main_pagees')->whereNotIn('id', [1])->get();
        $rowsss = MainPagee::where('id', 1)->first();
        return view('Admin.mainpage', compact('rows', 'rowss', 'rowsss'));

    }

    public function mainpagestoree(Request $request)
    {
        //        اعتبار سنجی اطلاعات ارسالی از فرم
        $this->validate(request(), [
            'body' => 'max:300',
        ]);

        $row = MainPagee::where('id', 1)->first();
        if (!empty($row)) {
            $row->update([
                'body' => request('body'),
                'updated_at' => Jalalian::now(),
            ]);
        } else {
            MainPagee::create([
                'body' => request('body'),
                'name' => request('name'),
                'site' => request('site'),
                'created_at' => Jalalian::now(),
                'updated_at' => Jalalian::now(),
            ]);
        }
        if (!empty($request->name)) {
            MainPagee::create([
                'name' => request('name'),
                'site' => request('site'),
                'created_at' => Jalalian::now(),
                'updated_at' => Jalalian::now(),
            ]);
        }
        $rows = DB::table('main_pages')->first();
        $rowss = DB::table('main_pagees')->whereNotIn('id', [1])->get();
        $rowsss = MainPagee::where('id', 1)->first();
        return view('Admin.mainpage', compact('rows', 'rowss', 'rowsss'));

    }

    public function delete($id)
    {
        $job = Job::where('id', $id)->first();
        $job->delete();

    }

    /*
     * پیش ثبت نام ها
     */
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pre_registration(Request $request)
    {

        $data = PreRegistration::
        when($request->get('name'), function ($query) use ($request) {
            $query->where('f_name', $request->name)
                ->orwhere('l_name', $request->name);
        })
            ->when($request->get('Fname'), function ($query) use ($request) {
                $query->where('Fname', 'like', '%' . $request->Fname . '%');
            })
            ->when($request->get('codemeli'), function ($query) use ($request) {
                $query->where('codemeli', 'like', '%' . $request->codemeli . '%');
            })
            ->when($request->get('paye'), function ($query) use ($request) {
                $query->where('paye', 'like', '%' . $request->paye . '%');
            })
            ->when($request->get('start_date'), function ($query) use ($request) {

                $query->where('created_at', '>=', $request->start_date);
            })
            ->when($request->get('end_date'), function ($query) use ($request) {

                $query->where('created_at', '<=', $request->end_date);
            })
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('Admin.pre_registration', compact('data'));
    }

    /*
     * گرفتن خروجی اکسل
     */
    /**
     * @return mixed
     */
    public function registrationExcel()
    {
        $data = PreRegistration::select('*')
            ->get()->toArray();

        return Excel::create('لیست پیش ثبت نام ها', function ($excel) use ($data) {
            $excel->sheet('users', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->download('xlsx');
    }

    public function converse()
    {
        $data = Home::where('place', 'سخن مدیر')->first();
        return view('Admin.home.converse', compact('data'));
    }

    public function conversestore(Request $request)
    {
        $this->validate(request(), [
            'title' => 'required',
            'place' => 'required',
            'body' => 'required',
        ]);

        $data = Home::where('place', 'سخن مدیر')->first();
        if ($data) {
            $data->update([
                'body' => request('body')
            ]);
        } else {
//        ایجاد ردیف در جدول home
            $data = Home::create([
                'title' => request('title'),
                'body' => request('body'),
                'place' => request('place'),
                'user_id' => auth()->user()->id,
                'created_at' => Jalalian::now(),
                'updated_at' => Jalalian::now(),
            ]);
        }


//        ایجاد فایل مناسب برای عکس ها
        $patchfile = $request->file('patchfile');
        if (!empty($request->patchfile)) {
            $delimages = HomeImage::where('matlab_id', $data->id)->get();
            foreach ($delimages as $delimage) {
                $delimage->delete();

            }


            $cover = $patchfile;
            $filename = time() . '.' . '.png';
            $path = public_path('/images/' . $filename);
            Image::make($cover->getRealPath())->resize(1275, 804)->save($path);
            $extension = $cover->getClientOriginalExtension();
            $mime = $cover->getClientMimeType();
            $original_filename = $cover->getClientOriginalName();


//  ایجاد یک ردیف برای ذخیره عکس در جدول imagehome
            HomeImage::create([
                'matlab_id' => $data->id,
                'mime' => $mime,
                'original_filename' => $original_filename,
                'filename' => $filename,
                'resize_image' => $filename,
            ]);
        }

        return redirect('admin/converse')->with('status', 'مطلب شما با موفقیت ایجاد شد');
    }

    public function mainpagedelete($id)
    {
        $site = MainPagee::find($id);
        $site->delete();
        return back();

    }

    public function karnamehcreate()
    {
        $class = clas::all();
        return view('Admin.karnameh.create', compact('class'));
    }

    public function karnamehstore(Request $request)
    {
        $this->validate(request(), [
            'start' => 'required',
            'class' => 'required',
            'name' => 'required',
        ]);
        $users = User::whereIn('class', $request->class)->where('role', 'دانش آموز')->select('id')->with('markitems')->get();
        $start = str_replace('/', '-', $request->start);
        $end = str_replace('/', '-', $request->get('date-picker-shamsi-list'));
        foreach ($users as $user) {
            foreach ($user->markitems->whereBetween('created_at', [$start, $end]) as $mark) {
                $karnameh = KarnamehAdmin::where('user_id', $user->id)->where('name', $request->name)->where('dars_id', $mark->items->dars)->first();
                if (!$karnameh) {
                    KarnamehAdmin::create([
                        'user_id' => $user->id,
                        'dars_id' => $mark->items->dars,
                        'name' => $request->name,
                        'count' => 1,
                        'mark' => $mark->mark
                    ]);
                } else {
                    $count = $karnameh->count + 1;
                    $karnameh->update([
                        'count' => $count,
                        'mark' => ($mark->mark + ($karnameh->mark) * $karnameh->count) / $count
                    ]);
                }
            }
        }
        alert()->success('کارنامه با موفقیت تولید شد.', 'عملیات موفق');
        return back();
    }

    public function karnamehshow($name, $class)
    {
        $students = User::where('class', $class)->
        with(['karnameadmin' => function ($query) use ($name) {
            $query->where('name', $name);
        }])->get();
        return view('Admin.karnameh.newstudent', compact('students', 'class'));
    }

    public function skarnamehshow($name, $user, $moadel)
    {
        $mykarnamehs = KarnamehAdmin::where('name', $name)->where('user_id', $user)->get();
        return view('Admin.karnameh.newskarnameh', compact('mykarnamehs', 'moadel'));
    }


    public function setting()
    {
        $setting = Setting::all()->first();

        $connect = Bigbluebutton::isConnect(); //default
        $connect2 = Bigbluebutton::server('server1')->isConnect(); //for specific server
//        dd($connect, $connect2);
        $gatway = Gateway::where('id', 1)->pluck('config')->first();
        $gatway = substr($gatway, 13, -2);

        return view('Admin.setting', ['setting' => $setting, 'connect' => $connect, 'gatway' => $gatway, 'connect2' => $connect2]);
    }

    public function settingstore(Request $request)
    {
        $status = 0;
        if ($request->finance_status == 'on') {
            $status = 1;
        }
        $row = Setting::all()->first();
        $row->update([
            'name' => $request->name,
            'BBB_SECURITY_SALT' => $request->BBB_SECURITY_SALT,
            'BBB_SECURITY_SALT_2' => $request->BBB_SECURITY_SALT_2,
            'BBB_SERVER_BASE_URL' => $request->BBB_SERVER_BASE_URL,
            'BBB_SERVER_BASE_URL_2' => $request->BBB_SERVER_BASE_URL_2,
            'finance_status' => $status,
            'finance_deadline' => $request->finance_deadline,
            'sky' => $request->sky,
        ]);
        $gatway = Gateway::where('id', 1)->first();
        $config = '{"' . 'merchant' . '":"' . $request->config . '"}';
        $gatway->update([
            'config' => $config
        ]);

        Artisan::call('cache:clear');
        Artisan::call('config:cache');
        $cover = $request->file('logo');
        if (!empty($cover)) {
//for resize
            $originalImage = $request->file('logo');
            $thumbnailImage = Image::make($originalImage);
            $thumbnailPath = public_path() . '/uploads/';
            $thumbnailImage->resize(150, 150);
            $thumbnailImage->save($thumbnailPath . time() . $originalImage->getClientOriginalName());
            $row->update([
                'logo' => time() . $originalImage->getClientOriginalName(),
            ]);
            alert()->success('موفق', 'ویرایش شما با موفقیت ثبت گردید!');
            return back();
        }
        alert()->success('موفق', 'ویرایش شما با موفقیت ثبت گردید!');
        return back();
    }

    public function settingstorename(Request $request)
    {
        $this->validate(request(), [
            'name' => 'required',
            'student' => 'required',
            'students' => 'required',
            'teacher' => 'required',
            'teachers' => 'required',
            'parent' => 'required',
            'parents' => 'required',
            'school' => 'required',
            'admin' => 'required',
            'mark1' => 'required',
            'mark2' => 'required',
            'mark3' => 'required',
        ]);
        $row = Setting::all()->first();
        $row->update([
            'name' => $request->name,
            'student' => $request->student,
            'students' => $request->students,
            'teacher' => $request->teacher,
            'teachers' => $request->teachers,
            'parent' => $request->parent,
            'parents' => $request->parents,
            'paye' => $request->paye,
            'school' => $request->school,
            'admin' => $request->admin,
            'mark1' => $request->mark1,
            'mark2' => $request->mark2,
            'mark3' => $request->mark3,
            'type_mark' => $request->type_mark,
        ]);
        Artisan::call('cache:clear');
        Artisan::call('config:cache');
        $cover = $request->file('logo');
        if (!empty($cover)) {
//for resize
            $originalImage = $request->file('logo');
            $thumbnailImage = Image::make($originalImage);
            $thumbnailPath = public_path() . '/uploads/';
            $thumbnailImage->resize(150, 150);
            $thumbnailImage->save($thumbnailPath . time() . $originalImage->getClientOriginalName());
            $row->update([
                'logo' => time() . $originalImage->getClientOriginalName(),
            ]);
            alert()->success('موفق', 'ویرایش شما با موفقیت ثبت گردید!');
            return back();
        }
        alert()->success('موفق', 'ویرایش شما با موفقیت ثبت گردید!');
        return back();
    }
}
