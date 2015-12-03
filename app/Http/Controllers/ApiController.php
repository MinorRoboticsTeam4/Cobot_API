<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as IlluminateResponse;

/**
 * Class ApiController
 * @package App\Http\Controllers
 */
class ApiController extends Controller
{
    /**
     * @var int
     */
    protected $statusCode = IlluminateResponse::HTTP_OK;

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @param $data
     * @param array $headers
     * @return mixed
     */
    public function respond($data, $headers = [])
    {
        return Response::json($data, $this->getStatusCode(), $headers);
    }

    /**
     * @param $message
     * @return mixed
     */
    public function respondWithError($message = "No message set, something went wrong!")
    {
        return $this->respond( [
                'message' => $message,
                'status_code' => $this->getStatusCode()
        ]);
    }

    /**
     * @param $message
     * @return mixed
     */
    public function respondWithSuccess($message = "No message set, something went wrong!")
    {
        return $this->respond([
                'message' => $message,
                'status_code' => $this->getStatusCode()
        ]);
    }

    /**
     * @param $errors
     * @param $message
     * @return mixed
     */
    public function respondWithValidationError($errors, $message)
    {
        $validationMessages = array();
        foreach($errors->all() as $error)
        {
            $validationMessages[] = $error;
        }

        return $this->respond([
                'message' => $message,
                'validation_messages' => $validationMessages,
                'status_code' => $this->getStatusCode()
        ]);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function respondCreated($data)
    {
        return $this
            ->setStatusCode(IlluminateResponse::HTTP_CREATED)
            ->respond($data);
    }

    /**
     * @param $message
     * @return mixed
     */
    public function respondUpdated($message)
    {
        return $this
            ->setStatusCode(IlluminateResponse::HTTP_OK)
            ->respondWithSuccess($message);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function respondNotFound($message)
    {
        return $this
            ->setStatusCode(IlluminateResponse::HTTP_NOT_FOUND)
            ->respondWithError($message);
    }

    /**
     * @param $errors
     * @param $message
     * @return mixed
     */
    public function respondValidationError($errors, $message)
    {
        return $this
            ->setStatusCode(IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->respondWithValidationError($errors, $message);
    }

    /**
     * @param $message
     * @return mixed
     */
    public function respondInsufficientPermissions($message)
    {
        return $this
            ->setStatusCode(IlluminateResponse::HTTP_UNAUTHORIZED)
            ->respondWithError($message);
    }

    /**
     * @param $message
     * @return mixed
     */
    public function respondForbiddenRequest($message)
    {
        return $this
            ->setStatusCode(IlluminateResponse::HTTP_FORBIDDEN)
            ->respondWithError($message);
    }

}