<?php

require_once __DIR__ . '/../services/ArticleService.class.php';

Flight::group('/articles', function () {
    /**
     * @OA\Post(
     *     path="/articles/add",
     *     tags={"articles"},
     *     summary="Create a new article",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"title", "content", "author_id"},
     *             @OA\Property(property="title", type="string", example="New Article"),
     *             @OA\Property(property="content", type="string", example="Article content here"),
     *             @OA\Property(property="author_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Article created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to create article"
     *     )
     * )
     */
    Flight::route('POST /add', function () {
        $data = Flight::request()->data->getData();
        $articleService = new ArticleService();
        if ($articleService->createArticle($data)) {
            Flight::json(['message' => 'Article created successfully'], 201);
        } else {
            Flight::json(['message' => 'Article could not be created'], 400);
        }
    });

    /**
     * @OA\Put(
     *     path="/articles/update/{article_id}",
     *     tags={"articles"},
     *     summary="Update an existing article",
     *     @OA\Parameter(
     *         name="article_id",
     *         in="path",
     *         required=true,
     *         description="Article ID to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"title", "content"},
     *             @OA\Property(property="title", type="string", example="Updated Title"),
     *             @OA\Property(property="content", type="string", example="Updated content here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article updated successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to update article"
     *     )
     * )
     */
    Flight::route('PUT /update/@article_id', function ($article_id) {
        $data = Flight::request()->data->getData();
        $articleService = new ArticleService();
        if ($articleService->updateArticle($article_id, $data)) {
            Flight::json(['message' => 'Article updated successfully']);
        } else {
            Flight::json(['message' => 'Article could not be updated'], 400);
        }
    });

    /**
     * @OA\Delete(
     *     path="/articles/delete/{article_id}",
     *     tags={"articles"},
     *     summary="Delete an article",
     *     @OA\Parameter(
     *         name="article_id",
     *         in="path",
     *         required=true,
     *         description="Article ID to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to delete article"
     *     )
     * )
     */
    Flight::route('DELETE /delete/@article_id', function ($article_id) {
        $articleService = new ArticleService();
        if ($articleService->deleteArticle($article_id)) {
            Flight::json(['message' => 'Article deleted successfully']);
        } else {
            Flight::json(['message' => 'Article could not be deleted'], 400);
        }
    });

    /**
     * @OA\Get(
     *     path="/articles",
     *     tags={"articles"},
     *     summary="Get all articles",
     *     @OA\Response(
     *         response=200,
     *         description="A list of articles",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="object", 
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Sample Article"),
     *                 @OA\Property(property="content", type="string", example="Content here"),
     *                 @OA\Property(property="author_id", type="integer", example=1)
     *             )
     *         )
     *     )
     * )
     */
    Flight::route('GET /', function () {
        $offset = Flight::request()->query['offset'] ?? 0;
        $limit = Flight::request()->query['limit'] ?? 25;
        $order = Flight::request()->query['order'] ?? '-id';
        $articleService = new ArticleService();
        Flight::json($articleService->getAllArticles($offset, $limit, $order));
    });

    /**
     * @OA\Get(
     *     path="/articles/{article_id}",
     *     tags={"articles"},
     *     summary="Get a single article by ID",
     *     @OA\Parameter(
     *         name="article_id",
     *         in="path",
     *         required=true,
     *         description="The ID of the article to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A single article",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="Sample Article"),
     *             @OA\Property(property="content", type="string", example="Content here"),
     *             @OA\Property(property="author_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found"
     *     )
     * )
     */
    Flight::route('GET /@article_id', function ($article_id) {
        $articleService = new ArticleService();
        $article = $articleService->getArticleById($article_id);
        if ($article) {
            Flight::json($article);
        } else {
            Flight::json(['message' => 'Article not found'], 404);
        }
    });

    /**
     * @OA\Get(
     *     path="/articles/user/{user_id}",
     *     tags={"articles"},
     *     summary="Get articles by user ID",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="The ID of the user whose articles to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of articles by the specified user",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="User Article"),
     *                 @OA\Property(property="content", type="string", example="Content written by user"),
     *                 @OA\Property(property="author_id", type="integer", example=1)
     *             )
     *         )
     *     )
     * )
     */
    Flight::route('GET /user/@user_id', function ($user_id) {
        $articleService = new ArticleService();
        Flight::json($articleService->getArticlesByUserId($user_id));
    });
});
