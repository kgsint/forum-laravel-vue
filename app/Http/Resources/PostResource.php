<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\ReplyResource;
use Spatie\LaravelMarkdown\MarkdownRenderer;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public $preserveKeys  = true;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'thread' => ThreadResource::make($this->thread),
            'user' => UserResource::make($this->user),
            'body' => app(MarkdownRenderer::class)->highlightTheme('material-theme-palenight')->toHtml($this->body), // markdown to html,
            'body_markdown' => $this->body,
            'created_at' => DateTimeResource::make($this->created_at),
            'parent' => PostResource::make($this->parent),
            'replies' => PostResource::collection($this->whenLoaded('replies')),
            'can' => [
                'update' => auth()->user()?->can('update', $this->resource) ?? false,
                'delete' => auth()->user()?->can('delete', $this->resource) ?? false,
            ]
        ];
    }
}
