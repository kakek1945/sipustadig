<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log significant actions
        if (auth()->check() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $action = match ($request->method()) {
                'POST' => 'create',
                'PUT', 'PATCH' => 'update',
                'DELETE' => 'delete',
                default => null,
            };

            if ($action) {
                $modelType = $this->getModelTypeFromRoute($request);

                if ($modelType) {
                    $modelId = $request->route()?->parameter('id') ?? $request->route()?->parameter(key($request->route()->parameters()) ?? '');
                    $modelId = is_numeric($modelId) ? (int) $modelId : null;

                    \App\Models\AuditLog::create([
                        'user_id' => auth()->id(),
                        'action' => $action,
                        'model_type' => $modelType,
                        'model_id' => $modelId,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'new_values' => $request->except(['password', '_token', '_method']),
                        'created_at' => now(),
                    ]);
                }
            }
        }

        return $response;
    }

    /**
     * Get model type from route name.
     */
    private function getModelTypeFromRoute(Request $request): ?string
    {
        $routeName = $request->route()?->getName() ?? '';

        $modelMap = [
            'members' => 'Member',
            'categories' => 'Category',
            'books' => 'Book',
            'book-copies' => 'BookCopy',
            'loans' => 'Loan',
            'fines' => 'Fine',
            'settings' => 'Setting',
        ];

        foreach ($modelMap as $key => $model) {
            if (str_contains($routeName, $key)) {
                return $model;
            }
        }

        return null;
    }
}