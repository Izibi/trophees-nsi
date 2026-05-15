<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Vote;
use App\Mail\VoteConfirmation;
use Carbon\Carbon;

class VoteController extends Controller
{
    private function normalizeEmail($email)
    {
        $email = strtolower($email);
        if (strpos($email, '+') !== false) {
            list($localPart, $domain) = explode('@', $email);
            $localPart = substr($localPart, 0, strpos($localPart, '+'));
            $email = $localPart . '@' . $domain;
        }
        if (strpos($email, '@') !== false) {
            list($localPart, $domain) = explode('@', $email);
            $localPart = str_replace('.', '', $localPart);
            $email = $localPart . '@' . $domain;
        }
        return $email;
    }

    public function vote(Request $request)
    {
        // Handle preflight OPTIONS request
        if ($request->isMethod('OPTIONS')) {
            return response()->json([], 200)
                ->header('Access-Control-Allow-Origin', 'https://trophees-nsi.fr')
                ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Accept');
        }

        $email = $request->input('email');
        $projectId = $request->input('project');

        if (!$email || $projectId === null) {
            return response()->json(['success' => false])
                ->header('Access-Control-Allow-Origin', 'https://trophees-nsi.fr');
        }

        // Validate project_id is between 0 and 5
        if ($projectId < 0 || $projectId > 5) {
            return response()->json(['success' => false])
                ->header('Access-Control-Allow-Origin', 'https://trophees-nsi.fr');
        }

        $normalizedEmail = $this->normalizeEmail($email);

        $today = Carbon::today();
        $existingVote = Vote::where('normalized_email', $normalizedEmail)
            ->whereDate('created_at', $today)
            ->first();

        if ($existingVote) {
            return response()->json([
                'success' => false,
                'error' => 'already_voted'
            ])->header('Access-Control-Allow-Origin', 'https://trophees-nsi.fr');
        }

        $confirmationString = md5($email . time());

        $vote = Vote::create([
            'email' => $email,
            'normalized_email' => $normalizedEmail,
            'project_id' => $projectId,
            'confirmation_string' => $confirmationString,
            'confirmed' => false
        ]);

        $confirmationLink = 'https://depot.trophees-nsi.fr/vote-du-public/confirmation?confirmation=' . $confirmationString;
        Mail::to($email)->send(new VoteConfirmation($confirmationLink));

        return response()->json(['success' => true])
            ->header('Access-Control-Allow-Origin', 'https://trophees-nsi.fr');
    }

    public function confirmation(Request $request)
    {
        $confirmationString = $request->input('confirmation');

        if (!$confirmationString) {
            return view('vote_confirmation', ['success' => false]);
        }

        $vote = Vote::where('confirmation_string', $confirmationString)->first();

        if (!$vote) {
            return view('vote_confirmation', ['success' => false]);
        }

        $vote->confirmed = true;
        $vote->save();

        return view('vote_confirmation', ['success' => true]);
    }
}
