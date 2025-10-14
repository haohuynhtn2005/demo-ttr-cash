<?php

namespace App\Controllers\Admin;

use App\Models\EmailHistoryModel;
use App\Resources\EmailHistoryResource;
use App\Services\JwtAuthService;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class EmailHistoryController extends ResourceController {
  // use ResponseTrait; // Already in ResourceController
  use ResponseTrait;

  protected $model;
  protected $jwtService;

  public function __construct() {
    $this->model = new EmailHistoryModel();
    $this->jwtService = new JwtAuthService();
  }

  /**
   * Return an array of resource objects, themselves in array format
   *
   * @return \CodeIgniter\HTTP\ResponseInterface
   */
  public function index() {
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
      $email_histories = $builder
        ->orderBy('created_at', 'DESC')
        ->findAll($limit, $offset);

      $resource = EmailHistoryResource::collection($email_histories);
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
      $message = "EmailHistoryController.index: ";
      $message .= $th->getFile() . " ";
      $message .= $th->getLine() . " ";
      $message .= $th->getMessage() . " ";
      log_message('error', $message);
      return $this->respond([
        'status' => false,
        // 'message' => $message,
        'message' => 'server error',
      ]);
    }
  }

  /**
   * Return the properties of a resource object
   *
   * @param int|string|null $id
   *
   * @return \CodeIgniter\HTTP\ResponseInterface
   */
  public function show($id = null) {
    $data = $this->model->find($id);
    if (!$data) {
      return $this->failNotFound('No email history found with id ' . $id);
    }
    $resource = new EmailHistoryResource($data);
    return $this->respond($resource->get());
  }

  /**
   * Create a new resource object, from "posted" parameters
   *
   * @return \CodeIgniter\HTTP\ResponseInterface
   */
  public function create() {
    $rules = [
      'recipient' => 'required|valid_email',
      'subject'   => 'required',
      'body'      => 'required',
      'status'    => 'required|in_list[0,1]', // Assuming 0 for failed, 1 for sent
    ];

    if (!$this->validate($rules)) {
      return $this->fail($this->validator->getErrors());
    }

    $data = $this->request->getPost();

    $id = $this->model->insert($data);
    if ($this->model->errors()) {
      return $this->fail($this->model->errors());
    }

    $response = [
      'status'   => 201,
      'error'    => null,
      'messages' => [
        'success' => 'Email history created successfully'
      ],
      'data' => $this->model->find($id)
    ];
    return $this->respondCreated($response);
  }

  /**
   * Delete the designated resource object from the model
   *
   * @param int|string|null $id
   *
   * @return \CodeIgniter\HTTP\ResponseInterface
   */
  public function delete($id = null) {
    $data = $this->model->find($id);
    if (!$data) {
      return $this->failNotFound('No email history found with id ' . $id);
    }

    $this->model->delete($id);

    return $this->respondDeleted(['id' => $id], 'Email history deleted successfully');
  }
}
