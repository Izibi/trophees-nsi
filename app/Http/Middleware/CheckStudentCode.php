<?php
//
// Student access is based on a project code stored in session.
// The code is re-checked on every request so that students are logged
// out as soon as the code is invalidated (renewed by the teacher) or
// the project is deleted.
//
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Project;

class CheckStudentCode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $session = $request->session();
        $project_id = $session->get('student_project_id');
        $project_code = $session->get('student_project_code');
        if(!$project_id || !$project_code) {
            return redirect('/connexion-eleve');
        }
        $project = Project::find($project_id);
        if(!$project || !hash_equals((string) $project->student_code, (string) $project_code)) {
            $session->forget(['student_project_id', 'student_project_code']);
            return redirect('/connexion-eleve')->with('error_message', 'Votre session n\'est plus valide, veuillez vous reconnecter avec votre code projet.');
        }
        $request->attributes->set('student_project', $project);
        return $next($request);
    }
}
