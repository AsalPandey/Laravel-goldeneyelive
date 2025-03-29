<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\JoinNowQueries;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Contact;
use App\Models\Courses;
use App\Models\NewsLetter;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function contact()
    {
        return view('site.contact.contact');
    }
    public function contactSubmit(Request $request)
    {
        $ValidatedData=$request->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
            'g-recaptcha-response' => 'recaptcha|required',
        ]);
        $contact = new Contact();
        $contact->name = $ValidatedData['name'];
        $contact->phone = $ValidatedData['phone'];
        $contact->email = $ValidatedData['email'];
        $contact->subject = $ValidatedData['subject'];
        $contact->message = $ValidatedData['message'];
        $contact->save();
        Alert::success('Success', 'Thank you for contacting us, we will get back to you soon');
        $to = "prasunpaudel2001@gmail.com";
        $data =[
            'dataType' => 'contactMail',
            'subject' => $ValidatedData['subject'],
            'message' => $ValidatedData['message'],
            'email' => $ValidatedData['email'],
            'name' => $ValidatedData['name'],
            'phone' => $ValidatedData['phone'],
        ];
        mail::to($to)->send(new ContactMail($data));
        return redirect()->back();
    }
    public function Newsletter(Request $request)
    {
        $ValidatedData=$request->validate([
            'Email' => 'required|email',
        ]);
        $NewsLetter = new NewsLetter();
        $NewsLetter->email = $ValidatedData['Email'];
        $NewsLetter->save();
        Alert::success('Success', 'Your email has been added to our newsletter');
        return redirect()->back();
    }
    //join now
    public function joinNow()
    {
        $data= [
            'courses' => Courses::all(),
        ];
        return view('site.join-now.join-now',$data);
    }
    public function joinNowSubmit(Request $request)
    {
        $ValidatedData=$request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'course' => 'required',
            'queries' => 'nullable|string',
            'g-recaptcha-response' => 'recaptcha|required',
        ]);
        $JoinNowQueries = new JoinNowQueries();
        $JoinNowQueries->firstName = $ValidatedData['firstName'];
        $JoinNowQueries->lastName = $ValidatedData['lastName'];
        $JoinNowQueries->email = $ValidatedData['email'];
        $JoinNowQueries->phone = $ValidatedData['phone'];
        $JoinNowQueries->address = $ValidatedData['address'];
        $course = Courses::where('slug',$ValidatedData['course'])->first();
        $JoinNowQueries->course = $course->name;
        $JoinNowQueries->queries = $ValidatedData['queries'];
        $JoinNowQueries->save();
        Alert::success('Success', 'Thank you for contacting us, we will get back to you soon');
        $to = "prasunpaudel2001@gmail.com";
        $data =[
            'dataType' => 'joinNow',
            'firstName' => $ValidatedData['firstName'],
            'lastName' => $ValidatedData['lastName'],
            'subject' => 'Join Now in '.$course->name,
            'email' => $ValidatedData['email'],
            'phone' => $ValidatedData['phone'],
            'address' => $ValidatedData['address'],
            'course' => $course->name,
            'queries' => $ValidatedData['queries'],
        ];
        mail::to($to)->send(new ContactMail($data));
        return redirect()->back();
    }
    public function contact_display()
    {
        $data = [
            'contacts' => Contact::all(),
        ];
        return view('admin.contact-display', $data);
    }
    public function join_now_display()
    {
        $data = [
            'joinNowQueries' => JoinNowQueries::all(),
        ];
        return view('admin.join-now-display', $data);
    }
}
