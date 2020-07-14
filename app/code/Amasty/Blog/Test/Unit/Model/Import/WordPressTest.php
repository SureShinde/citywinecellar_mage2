<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Test\Unit\Model\Import;

use Amasty\Base\Model\Serializer;
use Amasty\Blog\Model\Import\WordPress;
use Amasty\Blog\Model\Source\PostStatus;
use Amasty\Blog\Test\Unit\Traits;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class WordPressTest
 *
 * @see WordPress
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class WordPressTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers WordPress::importPosts
     */
    public function testImportPosts()
    {
        $model = $this->createPartialMock(WordPress::class, ['updatePost', 'savePost']);
        $connection = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->setMethods(['query'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $result = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->setMethods(['fetch_assoc'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $model->expects($this->once())->method('updatePost');
        $model->expects($this->once())->method('savePost');
        $result->expects($this->any())->method('fetch_assoc')->willReturnOnConsecutiveCalls(true, false, true, false);
        $connection->expects($this->any())->method('query')->willReturnOnConsecutiveCalls(false, $result, $result);

        $this->assertNull($this->invokeMethod($model, 'importPosts', [[], $connection]));
        $this->invokeMethod($model, 'importPosts', [['update' => true], $connection]);
        $this->invokeMethod($model, 'importPosts', [['update' => false], $connection]);
    }

    /**
     * @covers WordPress::savePost
     */
    public function testSavePost()
    {
        $model = $this->createPartialMock(WordPress::class, ['preparePostData']);
        $postRepository = $this->createMock(\Amasty\Blog\Api\PostRepositoryInterface::class);
        $postModel1 = $this->createMock(\Amasty\Blog\Api\Data\PostInterface::class);
        $postModel2 = $this->createMock(\Amasty\Blog\Api\Data\PostInterface::class);
        $connection = $this->createMock(\Magento\Framework\DataObject::class);

        $model->expects($this->any())->method('preparePostData')->willReturn($postModel1);
        $postModel1->expects($this->once())->method('setUrlKey');
        $postModel2->expects($this->any())->method('getPostId')->willReturnOnConsecutiveCalls(true, false);
        $postRepository->expects($this->any())->method('getByUrlKey')->willReturn($postModel2);

        $this->setProperty($model, 'postRepository', $postRepository);

        $this->invokeMethod($model, 'savePost', [['ID' => 1], $connection]);
        $this->invokeMethod($model, 'savePost', [['ID' => 1], $connection]);
    }

    /**
     * @covers WordPress::getStatus
     * @dataProvider getStatusDataProvider
     */
    public function testGetStatus($data, $result)
    {
        $model = $this->createPartialMock(WordPress::class, []);
        $this->assertEquals(
            $result,
            $this->invokeMethod($model, 'getStatus', [['post_status' => $data, 'post_password' => false]])
        );
    }

    /**
     * Data provider for getStatus test
     * @return array
     */
    public function getStatusDataProvider()
    {
        return [
            ['publish', PostStatus::STATUS_ENABLED],
            ['future', PostStatus::STATUS_SCHEDULED],
            ['draft', PostStatus::STATUS_DISABLED],
            ['test', PostStatus::STATUS_DISABLED],
        ];
    }

    /**
     * @covers WordPress::getPostBanner
     */
    public function testGetPostBanner()
    {
        $model = $this->createPartialMock(WordPress::class, ['getImageAlt']);
        $connection = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->setMethods(['query'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $result = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->setMethods(['fetch_assoc'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $serializer = $this->createPartialMock(Serializer::class, ['unserialize']);

        $connection->expects($this->any())->method('query')->willReturnOnConsecutiveCalls(false, $result);
        $model->expects($this->any())->method('getImageAlt')->willReturn('test');
        $result->expects($this->once())->method('fetch_assoc')
            ->willReturn(['post_id' => 5, 'meta_value' => 'a:2:{s:4:"file";s:5:"test1";s:5:"sizes";a:2:{i:0;i:1;i:1;i:2;}}']);
        $serializer->expects($this->any())->method('unserialize')->willReturn(['file' => 'test1', 'sizes' => [1, 2]]);

        $this->setProperty($model, 'serializer', $serializer);

        $this->invokeMethod($model, 'getPostBanner', [['ID' => 1], $connection]);
        $this->assertEquals(
            ['uploads/test1', 'test'],
            $this->invokeMethod($model, 'getPostBanner', [['ID' => 1], $connection])
        );
    }

    /**
     * @covers WordPress::getImageAlt
     */
    public function testGetImageAlt()
    {
        $model = $this->createPartialMock(WordPress::class, []);
        $connection = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->setMethods(['query'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $result = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->setMethods(['fetch_assoc'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $result->expects($this->once())->method('fetch_assoc')->willReturn(['meta_value' => 'test']);
        $connection->expects($this->any())->method('query')->willReturnOnConsecutiveCalls(false, $result);

        $this->invokeMethod($model, 'getImageAlt', [$connection, 1]);
        $this->assertEquals(
            'test',
            $this->invokeMethod($model, 'getImageAlt', [$connection, 1])
        );
    }

    /**
     * @covers WordPress::getRelationship
     */
    public function testGetRelationship()
    {
        $model = $this->createPartialMock(WordPress::class, []);
        $connection = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->setMethods(['query'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $result = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->setMethods(['fetch_assoc'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $connection->expects($this->any())->method('query')->willReturnOnConsecutiveCalls(false, $result, $result);
        $result->expects($this->any())->method('fetch_assoc')
            ->willReturnOnConsecutiveCalls(['term_id' => 5], false, ['term_id' => 5]);

        $this->setProperty($model, 'importedCategories', [5 => 3], WordPress::class);
        $this->setProperty($model, 'importedTags', [5 => 4], WordPress::class);

        $this->assertEquals(
            '',
            $this->invokeMethod($model, 'getRelationship', [['ID' => 1], $connection, 'category'])
        );
        $this->assertEquals(
            '3,',
            $this->invokeMethod($model, 'getRelationship', [['ID' => 1], $connection, 'category'])
        );
        $this->assertEquals(
            '4,',
            $this->invokeMethod($model, 'getRelationship', [['ID' => 1], $connection, 'post_tag'])
        );
    }

    /**
     * @covers WordPress::importTags
     */
    public function testImportTags()
    {
        $model = $this->createPartialMock(WordPress::class, ['updateTag', 'saveTag']);
        $connection = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->setMethods(['query'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $result = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->setMethods(['fetch_assoc'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $model->expects($this->once())->method('updateTag');
        $model->expects($this->once())->method('saveTag');
        $result->expects($this->any())->method('fetch_assoc')->willReturnOnConsecutiveCalls(true, false, true, false);
        $connection->expects($this->any())->method('query')->willReturnOnConsecutiveCalls(false, $result, $result);

        $this->assertNull($this->invokeMethod($model, 'importTags', [[], $connection]));
        $this->invokeMethod($model, 'importTags', [['update' => true], $connection]);
        $this->invokeMethod($model, 'importTags', [['update' => false], $connection]);
    }

    /**
     * @covers WordPress::saveTag
     */
    public function testSaveTag()
    {
        $model = $this->createPartialMock(WordPress::class, ['prepareTagData']);
        $tagRepository = $this->createMock(\Amasty\Blog\Api\TagRepositoryInterface::class);
        $tagModel1 = $this->createMock(\Amasty\Blog\Api\Data\TagInterface::class);
        $tagModel2 = $this->createMock(\Amasty\Blog\Api\Data\TagInterface::class);

        $model->expects($this->any())->method('prepareTagData')->willReturn($tagModel1);
        $tagModel1->expects($this->once())->method('setUrlKey');
        $tagModel2->expects($this->any())->method('getTagId')->willReturnOnConsecutiveCalls(true, false);
        $tagRepository->expects($this->any())->method('getByUrlKey')->willReturn($tagModel2);

        $this->setProperty($model, 'tagRepository', $tagRepository);

        $this->invokeMethod($model, 'saveTag', [['term_id' => 1]]);
        $this->invokeMethod($model, 'saveTag', [['term_id' => 1]]);
    }

    /**
     * @covers WordPress::importCategories
     */
    public function testImportCategories()
    {
        $model = $this->createPartialMock(WordPress::class, ['updateCategory', 'saveCategory']);
        $connection = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->setMethods(['query'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $result = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->setMethods(['fetch_assoc'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $model->expects($this->once())->method('updateCategory');
        $model->expects($this->once())->method('saveCategory');
        $result->expects($this->any())->method('fetch_assoc')->willReturnOnConsecutiveCalls(true, false, true, false);
        $connection->expects($this->any())->method('query')->willReturnOnConsecutiveCalls(false, $result, $result);

        $this->assertNull($this->invokeMethod($model, 'importCategories', [[], $connection]));
        $this->invokeMethod($model, 'importCategories', [['update' => true], $connection]);
        $this->invokeMethod($model, 'importCategories', [['update' => false], $connection]);
    }

    /**
     * @covers WordPress::saveCategory
     */
    public function testSaveCategory()
    {
        $model = $this->createPartialMock(WordPress::class, ['prepareCategoryData']);
        $categoryRepository = $this->createMock(\Amasty\Blog\Api\CategoryRepositoryInterface::class);
        $categoryModel1 = $this->createMock(\Amasty\Blog\Api\Data\CategoryInterface::class);
        $categoryModel2 = $this->createMock(\Amasty\Blog\Api\Data\CategoryInterface::class);

        $model->expects($this->any())->method('prepareCategoryData')->willReturn($categoryModel1);
        $categoryModel1->expects($this->once())->method('setUrlKey');
        $categoryModel2->expects($this->any())->method('getCategoryId')->willReturnOnConsecutiveCalls(true, false);
        $categoryRepository->expects($this->any())->method('getByUrlKey')->willReturn($categoryModel2);

        $this->setProperty($model, 'categoryRepository', $categoryRepository);

        $this->invokeMethod($model, 'saveCategory', [['term_id' => 1]]);
        $this->invokeMethod($model, 'saveCategory', [['term_id' => 1]]);
    }

    /**
     * @covers WordPress::getParentId
     */
    public function testGetParentId()
    {
        $model = $this->createPartialMock(WordPress::class, []);

        $this->setProperty($model, 'importedCategories', [5 => 6], WordPress::class);

        $this->assertEquals(0, $this->invokeMethod($model, 'getParentId', [['parent' => '0']]));
        $this->assertEquals(0, $this->invokeMethod($model, 'getParentId', [['parent' => 4]]));
        $this->assertEquals(6, $this->invokeMethod($model, 'getParentId', [['parent' => 5]]));
    }

    /**
     * @covers WordPress::getPath
     */
    public function testGetPath()
    {
        $model = $this->createPartialMock(WordPress::class, []);
        $parentCategory = $this->createMock(\Amasty\Blog\Api\Data\CategoryInterface::class);

        $parentCategory->expects($this->any())->method('getPath')->willReturn('1/2');

        $this->assertEquals(0, $this->invokeMethod($model, 'getPath', [['parent' => '0'], $parentCategory]));
        $this->assertEquals(0, $this->invokeMethod($model, 'getPath', [['parent' => 5], null]));
        $this->assertEquals('1/2/3', $this->invokeMethod($model, 'getPath', [['parent' => 5], $parentCategory]));
    }

    /**
     * @covers WordPress::getLevel
     */
    public function testGetLevel()
    {
        $model = $this->createPartialMock(WordPress::class, []);
        $parentCategory = $this->createMock(\Amasty\Blog\Api\Data\CategoryInterface::class);

        $parentCategory->expects($this->any())->method('getLevel')->willReturn(2);

        $this->assertEquals(1, $this->invokeMethod($model, 'getLevel', [['parent' => '0'], $parentCategory]));
        $this->assertEquals(1, $this->invokeMethod($model, 'getLevel', [['parent' => 5], null]));
        $this->assertEquals(3, $this->invokeMethod($model, 'getLevel', [['parent' => 5], $parentCategory]));
    }

    /**
     * @covers WordPress::importAuthors
     */
    public function testImportAuthors()
    {
        $model = $this->createPartialMock(WordPress::class, ['updateAuthor', 'saveAuthor']);
        $connection = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->setMethods(['query'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $result = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->setMethods(['fetch_assoc'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $model->expects($this->once())->method('updateAuthor');
        $model->expects($this->once())->method('saveAuthor');
        $result->expects($this->any())->method('fetch_assoc')->willReturnOnConsecutiveCalls(true, false, true, false);
        $connection->expects($this->any())->method('query')->willReturnOnConsecutiveCalls(false, $result, $result);

        $this->assertNull($this->invokeMethod($model, 'importAuthors', [[], $connection]));
        $this->invokeMethod($model, 'importAuthors', [['update' => true], $connection]);
        $this->invokeMethod($model, 'importAuthors', [['update' => false], $connection]);
    }

    /**
     * @covers WordPress::saveAuthor
     */
    public function testSaveAuthor()
    {
        $model = $this->createPartialMock(WordPress::class, ['prepareAuthorData']);
        $authorRepository = $this->createMock(\Amasty\Blog\Api\AuthorRepositoryInterface::class);
        $authorModel1 = $this->createMock(\Amasty\Blog\Api\Data\AuthorInterface::class);
        $authorModel2 = $this->createMock(\Amasty\Blog\Api\Data\AuthorInterface::class);

        $model->expects($this->any())->method('prepareAuthorData')->willReturn($authorModel1);
        $authorModel1->expects($this->once())->method('setUrlKey');
        $authorModel2->expects($this->any())->method('getAuthorId')->willReturnOnConsecutiveCalls(true, false);
        $authorRepository->expects($this->any())->method('getByUrlKey')->willReturn($authorModel2);

        $this->setProperty($model, 'authorRepository', $authorRepository);

        $this->invokeMethod($model, 'saveAuthor', [['ID' => 1]]);
        $this->invokeMethod($model, 'saveAuthor', [['ID' => 1]]);
    }

    /**
     * @covers WordPress::importComments
     */
    public function testImportComments()
    {
        $model = $this->createPartialMock(WordPress::class, ['saveComment']);
        $connection = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->setMethods(['query'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $result = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->setMethods(['fetch_assoc'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $model->expects($this->once())->method('saveComment');
        $result->expects($this->any())->method('fetch_assoc')->willReturnOnConsecutiveCalls(true, false);
        $connection->expects($this->any())->method('query')->willReturnOnConsecutiveCalls(false, $result);

        $this->assertNull($this->invokeMethod($model, 'importComments', [$connection]));
        $this->invokeMethod($model, 'importComments', [$connection]);
    }
}
