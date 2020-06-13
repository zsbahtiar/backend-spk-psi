<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(['prefix' => 'api/v1'], function () use ($router) {
	 /**
	 *
     * Create a new router
     * For mata user
     * 
     */

     $router->get('users/', 'UserController@index');
     $router->get('user/{id}', 'UserController@show');
     $router->post('user/create', 'UserController@store');
     $router->put('user/{id}/update', 'UserController@update');
     $router->delete('user/{id}/delete/', 'UserController@remove');

     /**
	 *
     * Create a new router
     * For DTA
     * 
     */

     $router->get('dta/', 'DtaController@index');
     $router->get('dta/{id}', 'DtaController@show');
     $router->post('dta/create', 'DtaController@store');
     $router->put('dta/{id}/update', 'DtaController@update');
     $router->delete('dta/{id}/delete/', 'DtaController@remove');

     /**
      *
     * Create a new router
     * For Alternatif
     * 
     */

     $router->get('alternatif/', 'AlternatifController@index');
     $router->get('alternatif/{id}', 'AlternatifController@show');
     $router->post('alternatif/create', 'AlternatifController@store');
     $router->put('alternatif/{id}/update', 'AlternatifController@update');
     $router->delete('alternatif/{id}/delete/', 'AlternatifController@remove');

     /**
      *
     * Create a new router
     * For Criteria
     * 
     */

     $router->get('criterias/', 'CriteriaController@index');
     $router->get('criteria/{id}', 'CriteriaController@show');
     $router->post('criteria/create', 'CriteriaController@store');
     $router->put('criteria/{id}/update', 'CriteriaController@update');
     $router->delete('criteria/{id}/delete/', 'CriteriaController@remove');


     /**
     *
     * Create a new router
     * For Operator
     * 
     */

     $router->get('operators/', 'OperatorController@index');
     $router->get('operator/{id}', 'OperatorController@show');
     $router->post('operator/create', 'OperatorController@store');
     $router->put('operator/{id}/update', 'OperatorController@update');
     $router->delete('operator/{id}/delete/', 'OperatorController@remove');
     
     /**
     *
     * Create a new router
     * For ValuesCriteria
     * 
     */

     $router->get('values/', 'ValuesAlternatifController@index');
     $router->get('value/{id}', 'ValuesAlternatifController@show');
     $router->post('value/create', 'ValuesAlternatifController@store');
     $router->put('value/{id}/update', 'ValuesAlternatifController@update');
     $router->delete('value/{id}/delete/', 'ValuesAlternatifController@remove');

     /**
     *
     * Create a new router
     * For Enumerisation
     * 
     */

     $router->get('enumerisation/', 'EnumerisationController@index');
     $router->get('enumerisation/{id}', 'EnumerisationController@show');
     $router->post('enumerisation/create', 'EnumerisationController@store');
     $router->put('enumerisation/{id}/update', 'EnumerisationController@update');
     $router->delete('enumerisation/{id}/delete/', 'EnumerisationController@remove');
     

     /**
     *
     * Create a new router
     * For Solving
     * 
     */

     $router->get('solve/{step}', 'ResultController@solve');
});
