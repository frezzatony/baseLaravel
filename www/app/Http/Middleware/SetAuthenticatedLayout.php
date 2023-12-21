<?php

namespace App\Http\Middleware;

use App\Services\System\ModuleMenuService;
use App\Services\System\ModuleService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetAuthenticatedLayout
{
    public function handle(Request $request, Closure $next): Response
    {
        $isAjax = $request->ajax();
        if (!$isAjax) {
            if (!$this->setUserModules()) {
                return redirect()->route('public.auth.logout')->with('error', 'Não foi possível efetuar o login. Erro: Módulo. Consulte o Administrador.');
            }
            $this->setModuleActive();
            $this->setModuleMenu();
        }

        return $next($request);
    }

    private function setUserModules()
    {
        $userModules = Auth::user()->is_master
            ? ModuleService::findAllByFilters(['is_active' => 'true'], ['order_by' => 'list_order ASC', 'include_master' => true])
            : ModuleService::findAllModulesActiveByUserId(Auth::user()->id);
        if (empty($userModules->first())) {
            return false;
        }
        view()->share(
            '_user_modules',
            $userModules
        );
        return true;
    }

    private function setModuleActive()
    {
        $moduleId = Auth::user()->attributes()->get('module_id');
        $module = view()->shared('_user_modules')->filter(function ($module) use ($moduleId) {
            return $module->id == $moduleId;
        })->first();
        view()->share('_active_module', $module);
    }


    private function setModuleMenu()
    {
        $moduleMenu = ModuleMenuService::getModuleMenuByModuleIdAndUserId(view()->shared('_active_module')->id, Auth::user()->id);
        view()->share('_module_menu', $moduleMenu);
    }
}
