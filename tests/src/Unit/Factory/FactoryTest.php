<?php

declare(strict_types=1);

namespace Tests\Unit\Factory;

use PHPUnit\Framework\TestCase;
use Tests\App\Database\Comment;
use Tests\App\Database\Post;
use Tests\App\Database\User;
use Tests\Database\Factory\CommentFactory;
use Tests\Database\Factory\PostFactory;
use Tests\Database\Factory\UserFactory;

final class FactoryTest extends TestCase
{
    public function testCreateEntity(): void
    {
        $user = UserFactory::new()->createOne();
        $post = PostFactory::new()->createOne();
        $comment = CommentFactory::new()->createOne();

        $this->assertInstanceOf(User::class, $user);
        $this->assertIsString($user->firstName);
        $this->assertIsString($user->lastName);
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->birthday);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertIsArray($post->comments);
        $this->assertCount(3, $post->comments);
        $this->assertInstanceOf(Comment::class, $post->comments[array_key_first($post->comments)]);
        $this->assertIsString($post->content);
        $this->assertInstanceOf(User::class, $post->author);
        $this->assertInstanceOf(\DateTimeImmutable::class, $post->publishedAt);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertIsString($comment->text);
        $this->assertInstanceOf(User::class, $comment->author);
        $this->assertInstanceOf(\DateTimeImmutable::class, $comment->postedAt);
    }

    public function testCreateMultiple(): void
    {
        $users = UserFactory::new()->times(2)->create();

        $this->assertCount(2, $users);

        // with different data
        $first = $users[array_key_first($users)];
        $second = $users[array_key_last($users)];

        $this->assertNotEquals($first->firstName, $second->firstName);
        $this->assertNotEquals($first->lastName, $second->lastName);
    }

    public function testCreateNullableNotFilled(): void
    {
        $user = UserFactory::new()->createOne();

        $this->assertNull($user->city);
    }

    public function testAfterCreateCallback(): void
    {
        $post = PostFactory::new()->afterCreate(fn(Post $post) => $post->content = 'changed by callback')->createOne();

        $this->assertSame('changed by callback', $post->content);
    }

    public function testCreateWithReplaces(): void
    {
        $post = PostFactory::new(['content' => 'changed by replaces array'])->createOne();

        $this->assertSame('changed by replaces array', $post->content);
    }
}
