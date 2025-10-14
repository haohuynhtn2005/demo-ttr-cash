<?php

namespace App\Controllers\Admin;

use App\Models\SystemSettingModel;
use App\Requests\SystemSetting\CreateSystemSettingRequest;
use App\Resources\SystemSettingResource;
use App\Services\JwtAuthService;
use CodeIgniter\RESTful\ResourceController;

class SystemSettingController extends ResourceController
{
  protected $model;
  protected $jwtService;
  protected $controllerName = 'Setting';

  public function __construct()
  {
    $this->model = new SystemSettingModel();
    $this->jwtService = new JwtAuthService();
  }

  public function index()
  {
    try {
      //Authorization
      $auth = $this->jwtService->authenticateUser();
      if (!$auth['status']) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.no_authorize')
        ], 403);
      }
      $userInfo = (array) $auth['user_info'];
      $roleId = $userInfo['role_id'];
      if (!isAdmin($roleId)) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.no_authorize')
        ], 403);
      }

      // Validation
      $rules = [
        'page'   => 'permit_empty|integer|is_natural_no_zero',
        'limit'  => 'permit_empty|integer|is_natural_no_zero',
        'search' => 'permit_empty|max_length[100]',
      ];
      $messages = [
        'page.is_natural_no_zero'  => 'Page must be a positive number.',
        'limit.is_natural_no_zero' => 'Limit must be a positive number.',
        'search.max_length'        => 'Search term must not exceed 100 characters.',
      ];

      $validation = \Config\Services::validation();
      $validation->setRules($rules, $messages);
      $request = $this->request->getGet();
      if (!$validation->run($request)) {
        return $this->respond([
          'status' => false,
          'message' => 'Validation failed',
          'errors' => $validation->getErrors()
        ], 422);
      }

      $page  = isset($request['page']) ? (int) $request['page'] : 1;
      $limit = isset($request['limit']) ? (int) $request['limit'] : 10;
      $search = $request['search'] ?? "";
      $status = $request['status'] ?? "";

      $offset = ($page - 1) * $limit;

      $builder = $this->model;
      if (!empty($search)) {
        $builder->where('name', $search);
      }

      if ($status != '' && !is_null($status)) {
        $builder->where('status', $status);
      }

      $total = $builder->countAllResults(false);
      $system_settings = $builder
        ->orderBy('created_at', 'DESC')
        ->findAll($limit, $offset);

      $resource = SystemSettingResource::collection($system_settings);
      return $this->respond([
        'status' => true,
        'data' => $resource,
        'pagination' => [
          'total' => $total,
          'limit' => $limit,
          'page' => $page,
          'pages' => ceil($total / $limit)
        ]
      ]);
    } catch (\Throwable $th) {
      $message = "SystemSettingController.index: ";
      $message .= $th->getFile() . " ";
      $message .= $th->getLine() . " ";
      $message .= $th->getMessage() . " ";
      log_message('error', $message);
      return $this->respond([
        'status' => false,
        'message' => 'An error occurred during processing. Please try again later.'
      ]);
    }
  }

  public function create()
  {
    try {
      //Authorization
      $auth = $this->jwtService->authenticateUser();
      if (!$auth['status']) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.no_authorize')
        ], 403);
      }
      $userInfo = (array) $auth['user_info'];
      $roleId = $userInfo['role_id'];
      if (!isAdmin($roleId)) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.no_authorize')
        ], 403);
      }

      //Initial request
      $request = $this->request->getJSON(true);
      //Validation
      $rules    = CreateSystemSettingRequest::rules();
      $messages = CreateSystemSettingRequest::messages();
      if (!$this->validateData($request, $rules, $messages)) {
        return $this->respond([
          'status' => false,
          'errors' => $this->validator->getErrors(),
        ]);
      }
      $name = $request['name'];
      $description = $request['description'] ?? null;
      $status = $request['status'] ?? 0;

      //Process
      $exist = $this->model->where('name', $name)->first();
      if ($exist) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.model_existed', ['name' => $this->controllerName]),
        ]);
      }

      $insertedId = $this->model->insert([
        'name' => $name,
        'description' => $description,
        'status' => (int)$status,
      ]);
      if (!$insertedId) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.model_create', ['name' => $this->controllerName])
        ], 404);
      }

      return $this->respond([
        'status' => true,
        'message' => lang('Common.success.model_create', ['name' => $this->controllerName])
      ], 201);
    } catch (\Throwable $th) {
      $message = "SystemSettingController.create: ";
      $message .= $th->getFile() . " ";
      $message .= $th->getLine() . " ";
      $message .= $th->getMessage() . " ";
      log_message('error', $message);
      return $this->respond([
        'status' => false,
        'message' => 'An error occurred during processing. Please try again later.'
      ]);
    }
  }

  public function show($id = null)
  {
    try {
      //Authorization
      $auth = $this->jwtService->authenticateUser();
      if (!$auth['status']) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.no_authorize')
        ], 403);
      }
      $userInfo = (array) $auth['user_info'];
      $roleId = $userInfo['role_id'];
      if (!isAdmin($roleId)) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.no_authorize')
        ], 403);
      }

      $data = $this->model->find($id);
      if (!$data) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.not_found', ['name' => $this->controllerName])
        ], 404);
      }

      $resource = new SystemSettingResource($data);
      return $this->respond([
        'status' => true,
        'data' => $resource,
      ]);
    } catch (\Throwable $th) {
      $message = "SystemSettingController.show: ";
      $message .= $th->getFile() . " ";
      $message .= $th->getLine() . " ";
      $message .= $th->getMessage() . " ";
      log_message('error', $message);
      return $this->respond([
        'status' => false,
        'message' => 'An error occurred during processing. Please try again later.'
      ]);
    }
  }

  public function update($id = null)
  {
    try {
      //Authorization
      $auth = $this->jwtService->authenticateUser();
      if (!$auth['status']) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.no_authorize')
        ], 403);
      }
      $userInfo = (array) $auth['user_info'];
      $roleId = $userInfo['role_id'];
      if (!isAdmin($roleId)) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.no_authorize')
        ], 403);
      }

      $role = $this->model->find($id);
      if (!$role) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.not_found', ['name' => $this->controllerName])
        ], 404);
      }

      //Initial request
      $request = $this->request->getJSON(true);
      //Validation
      $rules    = CreateSystemSettingRequest::rules();
      $messages = CreateSystemSettingRequest::messages();
      if (!$this->validateData($request, $rules, $messages)) {
        return $this->respond([
          'status' => false,
          'errors' => $this->validator->getErrors(),
        ]);
      }

      //Process
      $this->model->update($id, [
        'name' => $request['name'],
        'role_type' => $request['role_type'],
        'description' => $request['description'],
      ]);

      return $this->respond([
        'status' => true,
        'message' => lang('Common.success.model_update', ['name' => $this->controllerName])
      ]);
    } catch (\Throwable $th) {
      $message = "SystemSettingController.update: ";
      $message .= $th->getFile() . " ";
      $message .= $th->getLine() . " ";
      $message .= $th->getMessage() . " ";
      log_message('error', $message);
      return $this->respond([
        'status' => false,
        'message' => 'An error occurred during processing. Please try again later.'
      ]);
    }
  }

  public function delete($id = null)
  {
    try {
      //Authorization
      $auth = $this->jwtService->authenticateUser();
      if (!$auth['status']) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.no_authorize')
        ], 403);
      }
      $userInfo = (array) $auth['user_info'];
      $roleId = $userInfo['role_id'];
      if (!isAdmin($roleId)) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.no_authorize')
        ], 403);
      }

      $role = $this->model->find($id);
      if (!$role) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.not_found', ['name' => $this->controllerName])
        ], 404);
      }
      $this->model->delete($id);

      return $this->respond([
        'status' => true,
        'message' => lang('Common.success.model_delete', ['name' => $this->controllerName])
      ]);
    } catch (\Throwable $th) {
      $message = "SystemSettingController.delete failed: ";
      $message .= $th->getFile() . " ";
      $message .= $th->getLine() . " ";
      $message .= $th->getMessage() . " ";
      log_message('error', $message);
      return $this->respond([
        'status' => false,
        'message' => 'An error occurred during processing. Please try again later.'
      ]);
    }
  }
}
