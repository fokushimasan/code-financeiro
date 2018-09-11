<?php

namespace CodeFin\Http\Controllers\Api;

use CodeFin\Criteria\FindRootCategoriesCriteria;
use CodeFin\Criteria\WithDepthCategoriesCriteria;
use CodeFin\Http\Controllers\Controller;
use CodeFin\Http\Controllers\Response;
use CodeFin\Http\Requests\CategoryRequest;
use CodeFin\Repositories\CategoryRepository;

/**
 * Class BankAccountsController.
 *
 * @package namespace CodeFin\Http\Controllers;
 */
class CategoriesController extends Controller
{
    /**
     * @var BankAccountRepository
     */
    protected $repository;

    /**
     * BankAccountsController constructor.
     *
     * @param BankAccountRepository $repository

     */
    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
        $this->repository->pushCriteria(new WithDepthCategoriesCriteria());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(new FindRootCategoriesCriteria());
        $categories = $this->repository->all();

        return $categories;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  BankAccountCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CategoryRequest $request)
    {
        $category = $this->repository->skipPresenter()->create($request->all());
        $this->repository->skipPresenter(false);
        $category = $this->repository->find($category->id);

        return response()->json($category,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = $this->repository->find($id);

        return response()->json($category,200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  BankAccountUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(CategoryRequest $request, $id)
    {
        $category = $this->repository->update($request->all(), $id);

        return response()->json($category,200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->repository->delete($id);

        return response()->json([],204);
    }
}
