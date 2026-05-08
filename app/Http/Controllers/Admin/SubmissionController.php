<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\JoinNowQuery;
use App\Models\NewsLetter;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class SubmissionController extends Controller
{
    /**
     * Display Contact Submissions
     */
    public function contact_display(Request $request)
    {
        $search = $request->input('search');

        $contacts = Contact::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.contact-display', compact('contacts'));
    }

    public function updateContactStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:new,reviewed,contacted,resolved,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        $contact = Contact::findOrFail($id);

        $data = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ];

        if ($request->status !== 'new' && ! $contact->replied_at) {
            $data['replied_at'] = now();
        }

        $contact->update($data);
        Alert::success('Success', 'Inquiry details updated.');

        return back();
    }

    public function destroyContact($id)
    {
        Contact::findOrFail($id)->delete();
        Alert::success('Removed', 'The inquiry has been deleted.');

        return back();
    }

    /**
     * Display Join Now Submissions
     */
    public function join_now_display(Request $request)
    {
        $search = $request->input('search');

        $joinNowQueries = JoinNowQuery::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('firstName', 'like', "%{$search}%")
                        ->orWhere('lastName', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.join-now-display', compact('joinNowQueries'));
    }

    public function updateJoinStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:new,reviewed,contacted,enrolled,resolved,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        $query = JoinNowQuery::findOrFail($id);

        $data = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ];

        if ($request->status !== 'new' && ! $query->followed_up_at) {
            $data['followed_up_at'] = now();
        }

        $query->update($data);
        Alert::success('Success', 'Enrollment details updated.');

        return back();
    }

    public function destroyJoin($id)
    {
        JoinNowQuery::findOrFail($id)->delete();
        Alert::success('Removed', 'The enrollment record has been deleted.');

        return back();
    }

    /**
     * Display Newsletter Subscribers
     */
    public function newsletter_display(Request $request)
    {
        $search = $request->input('search');

        $subscribers = NewsLetter::query()
            ->when($search, function ($query, $search) {
                $query->where('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.newsletter.index', compact('subscribers'));
    }

    public function destroyNewsletter($id)
    {
        NewsLetter::findOrFail($id)->delete();
        Alert::success('Removed', 'Subscriber has been removed from the list.');

        return back();
    }

    /**
     * Bulk Delete Submissions
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer',
            'type' => 'required|in:contact,join_now,newsletter',
        ]);

        $ids = $request->input('ids');
        $type = $request->input('type');

        switch ($type) {
            case 'contact':
                Contact::whereIn('id', $ids)->delete();
                break;
            case 'join_now':
                JoinNowQuery::whereIn('id', $ids)->delete();
                break;
            case 'newsletter':
                NewsLetter::whereIn('id', $ids)->delete();
                break;
        }

        Alert::success('Bulk Action Complete', count($ids).' records have been removed successfully.');

        return back();
    }
}
