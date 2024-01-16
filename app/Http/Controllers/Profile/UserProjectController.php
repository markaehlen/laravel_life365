<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserProject;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\Web\ProfileRequest;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use Session;

class UserProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        session()->forget(['project']);    
        $user = Auth::guard('user')->user();

        return Inertia::render('Profile/UserProjects', [
            'filters' => Request::all('search', 'sort'),
            'projects' => UserProject::where('user_id', $user->id)->filter(Request::only('search', 'sort'))
                ->paginate(Config::get('pagination.admin_per_page'))
                ->withQueryString()
                ->through(function ($project, $index) {
                    $inputJSON = json_decode(json_encode(json_decode($project->details)), true);
                    
                    return [
                        'id' => $project->id,
                        'srno' => ($index + 1),
                        'title' => $project->title,
                        'date' => $inputJSON['project']['projectData']['date'],
                        'description' => $project->description,
                        'unique_id' => $project->unique_id,
                        'created_at' => $project->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
                    ];
                }),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(ProfileRequest $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($projectID)
    {
        try {
            $project = UserProject::find($projectID);

            if ($project) {
                if (session('projectID') == $project->unique_id) {
                    session()->forget('projectID');
                }
                $project->delete();
                return Redirect::back()->with('success', 'Project has been deleted successfully.');
            } else {
                return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
            }
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
        }
    }
}
