<?php

namespace App\Http\Controllers;

use App\Http\Resources\CutomerResource;
use App\Imports\UsersImport;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;
use Excel;

class CustomerController extends Controller
{
    protected $customer_repo;

    public function __construct(CustomerRepository $customer_repo)
    {
        $this->customer_repo = $customer_repo;
    }

    /**
     * @OA\Get(
     *      path="/customers",
     *      operationId="getCustomerList",
     *      tags={"Customers"},
     *      summary="Get list of customers",
     *      description="Returns list of customers",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function index()
    {
        //get customers
        $customer = $this->customer_repo->get();
        if (isset($customer)) {
            return response()->json($customer);
        }
        return $response = response()->json(['data' => 'Resource not found'], 404);

    }

    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *      path="/customer",
     *      operationId="storeCustomer",
     *      tags={"Customers"},
     *      summary="Store new Customer",
     *      description="Returns customer data",
     *      @OA\RequestBody(
     *          required=true,
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function store(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
        ]);

        try {
            $user = $this->customer_repo->save();
            //return successful response
            return response()->json(['res' => 'Customer created successfully.!', 'status' => 1]);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['res' => 'Customer Registration Failed!', 'status' => 0]);
        }
    }

    /**
     * @OA\Get(
     *      path="/customers/{id}",
     *      operationId="getCustomersById",
     *      tags={"Customers"},
     *      summary="Get Customers information",
     *      description="Returns Customers data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Customers id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function show($id)
    {
        if ($customer = $this->customer_repo->get($id)->first()) {
            return new CutomerResource($customer);
        }

        return $response = response()->json(['data' => 'Resource not found'], 404);
    }


    public function edit($id)
    {
        //
    }

    /**
     * @OA\Put(
     *      path="/customers/{id}",
     *      operationId="updatecustomers",
     *      tags={"Customers"},
     *      summary="Update existing customers",
     *      description="Returns updated customers data",
     *      @OA\Parameter(
     *          name="id",
     *          description="customers id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *      ),
     *      @OA\Response(
     *          response=202,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
        ]);

        try {
            $customer = $this->customer_repo->update($id);
            //return successful response
            return response()->json(['res' => 'Customer updated successfully.!', 'status' => 1]);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['res' => 'Customer Registration Failed!', 'status' => 0]);
        }
    }

    /**
     * @OA\Delete(
     *      path="/customers/{id}",
     *      operationId="deleteCustomers",
     *      tags={"Customers"},
     *      summary="Delete existing customers",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="Customers id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function destroy($id)
    {
        if ($customer = $this->customer_repo->delete($id)) {
            return response()->json(['res' => 'Customer delete successfully.!', 'status' => 1]);
        }
        return response()->json(['res' => 'Customer delete Failed!', 'status' => 0]);
    }

    /**
     * @OA\Post(
     *      path="/customers-import",
     *      operationId="importCustomers",
     *      tags={"Imposert Customer CSV"},
     *      summary="Update existing customers or adding",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="file",
     *          description="file",
     *          required=true,
     *          in="path",
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function fileImport(Request $request)
    {
        if ($request->file) {
            $import = new UsersImport();
            \Excel::import($import, $request->file);
            return response()->json([
                'message' => $import->data->count() . " records successfully uploaded"
            ]);
        } else {
            throw new \Exception('No file was uploaded', Response::HTTP_BAD_REQUEST);
        }
    }

    public function search(){
        $customer = $this->customer_repo->get();

    }
}
