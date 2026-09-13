<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\GroupMembership;
use Symfony\Component\HttpFoundation\Response;

class GroupAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $groupId = $request->route('groupId');
        
        if (!$groupId) {
            return redirect()->route('home')
                ->with('error', 'Invalid group ID.');
        }

        $isAdmin = GroupMembership::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('is_admin', true)
            ->where('status', 'approved')
            ->exists();

        if (!$isAdmin) {
            return redirect()->route('groups.show', $groupId)
                ->with('error', 'You do not have permission to access the admin dashboard.');
        }

        return $next($request);
    }
}
