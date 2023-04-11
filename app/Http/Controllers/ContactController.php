<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;


class ContactController extends Controller
{
    public function submit(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            if(config('app.app_write_emails_file', false)) {
                $emails_file = config('app.app_emails_file_name', 'contact_emails.txt');
                if (Storage::missing($emails_file)) {
                    Storage::put($emails_file, $request->input('email'));
                } else {
                    $emails = Storage::get($emails_file);
                    if (!Str::contains($emails, $request->input('email'))) {
                        Storage::append($emails_file, $request->input('email'));
                    }
                }
            }
            
            // Send mail with contact form data to admin
            $emailData = [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'subject' => $request->input('subject'),
                'content' => $request->input('message'),
            ];

            $emailSent = Mail::send('emails.contact-mail', $emailData, function ($message) use ($emailData) {
                $message->from($emailData['email'], $emailData['name'])
                    ->to(config('mail.from.address'), config('mail.from.name'))
                    ->subject($emailData['subject']);
            });
    
            if (!$emailSent) {
                return Redirect::back()->withErrors(['email' => 'Sending email failed. Please try again.']);
            }
    
            // Redirect the user back to the contact page with a success message
            return redirect()->route('contact')->with('success', 'Your message has been sent successfully!');
       
        } catch (ValidationException $exception) {
            return redirect()->route('contact')->withErrors($exception->validator);
        }
    }
}
