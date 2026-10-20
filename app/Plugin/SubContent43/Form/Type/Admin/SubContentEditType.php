<?php
    /**
     * @version EC-CUBE4.3
     * @copyright 株式会社 翔 kakeru.co.jp
     * @author
     * 2026年05月10日作成
     *
     * app/Plugin/SubContent43/Form/Type/Admin/SubContentEditType.php
     *
     *
     * サブコンテンツ エディト タイプ
     *
     *
     *
     *                             C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
     ******************************************************/
    namespace Plugin\SubContent43\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints as Assert;
use Plugin\SubContent43\Repository\ConfigRepository;
use Plugin\SubContent43\Service\ScService;
use Plugin\SubContent43\Validator as Valid;
use Plugin\SubContent43\Form\Type\ConfigEntityType;

class SubContentEditType extends AbstractType
{
    /**
     * 
     */
    protected $entityManager;
  
    /**
    * @var ConfuigRepository
    */
    protected $ConfigRepository;

   /**
    * @var ScService
    */
    protected $ScService;
    /**
    * @var int id
    */
    protected $Id;

   // protected $HolderJs;


   /**
    * @param DeviceTypeRepository $deviceTypeRepository
    */
    public function __construct(EntityManagerInterface $entityManager
                                ,ConfigRepository $ConfigRepository
                                ,ScService $ScService
                                )

    {
        $this->entityManager = $entityManager;
        $this->ConfigRepository =$ConfigRepository;
        $this->ScService = $ScService;

    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

       
        
        
        $TextareaDef = $this->ScService->GetConfig('Plugin_SubContent_Textarea_def');
        $builder
            ->add('title', TextType::class, [
                'label' => 'タイトル',
                'required' => true,
                'attr'=>[
                        'placeholder'=>'タイトルを入れてください'
                ],
                'constraints' => [
                      new Assert\NotBlank(['message'=>'タイトルを入れてください']),
                ],
            ])
            ->add('kbn', ChoiceType::class, [
                'label' => '区分',
                'constraints' => [
                      new Assert\NotBlank(['message'=>'区分を入れてください']),
                ],
                'choices' => $this->ScService->GetYaml('SubContent_Kbn.yaml',true),
                'required' => true,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('Config', ConfigEntityType::class, [
                'label' => 'ホルダー',
                'placeholder' => '選択してください',
                'required' => false,
                'constraints' => [
                    new Valid\UniqueSp([
                        'message'=>'SubContent.Parameter.ErrorMessage03',
                        'SubContent'   => $options['data'],            
                    ])
                ],
            ])
            ->add('holder', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Regex(['pattern' => $this->ScService->GetConfig('Plugin_SubContent_Holder_Limit'),'message'=>'SubContent.Common.ErrorMessage01']),
                    new Valid\UniqueParts([
                        'message'=>'SubContent.Parameter.ErrorMessage03',
                        'SubContent'   => $options['data'],            
                    ])
                ],
            ])
            ->add('parameter', TextType::class, [
                'label' => 'パラメーター',
                'required' => false,
                'attr'=>[
                        'placeholder'=>'パラメーターを入れてください'
                ],
                'constraints' => [
                    new Valid\Parameter(['Data'   =>  $options['data']]),
                ],
            ])
            ->add('author', TextType::class, [
                'label' => 'author',
                'required' => false,
                'attr'=>[
                        'placeholder'=>'authorを入れてください'
                ],
                'constraints' => [
                      new Assert\Length(['max' =>  $this->ScService->GetConfig('eccube_stext_len')]),
                ],
            ])
            ->add('description', TextType::class, [
                'label' => 'description',
                'required' => false,
                'attr'=>[
                        'placeholder'=>'descriptionを入れてください',
                ],
                'constraints' => [
                         new Assert\Length(['max' => $this->ScService->GetConfig('eccube_stext_len')]),
                ],
            ])
            ->add('meta_robots', TextType::class, [
                'label' => 'robots',
                'required' => false,
                'attr'=>[
                        'placeholder'=>'検索されたくない場合は　noindex',
                ],
                'constraints' => [
                         new Assert\Length(['max' => $this->ScService->GetConfig('eccube_stext_len')]),
                ],
            ])
            ->add('meta_tags', TextType::class, [
                'label' => 'meta_tags',
                'required' => false,
                'attr'=>[
                        'placeholder'=>'meta tag入れてください'
                ],
                'constraints' => [
                       new Assert\Length(['max' => $this->ScService->GetConfig('eccube_stext_len')]),
                ],
            ])
            ->add('html', TextareaType::class, [
                'label' => 'HTML',
                'required' =>true,
                'attr'=>[
                    'placeholder' => 'HTMLを入れてください',
                    'rows'=> $TextareaDef['Html'],
                ],
                'constraints' => [
                      new Assert\NotBlank(['message'=>'HTMLを入れてください']),
                ],
            ])
            ->add('css', TextareaType::class,
                [
                'label' => 'CSS',
                'required' =>false,
                 'attr'=>[
                    'placeholder' => 'CSSを入れてください',
                     'rows'=> $TextareaDef['Stylesheet'],
                    ]
                ])
            ->add('image_file',FileType::class, [
                'multiple' => true,
                'required' => false,
                'mapped' => false,
            ])
            ->add('add_images', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'prototype' => true,
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('delete_images', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'prototype' => true,
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
/* 追加項目 追加項目として TEXTを用意しています
            ->add('text01', TextType::class, [
                'label' => 'サムネイル', 
                'required' => false,
                'attr'=>[
                        'placeholder'=>$this->ScService->GetConfig('Plugin_SubContent_thumbnail_name').'のサムネイルイメージ名を入れてください'
                ],

            ]);
   
            ->add('text02', TextType::class, [
                'label' => 'メインイメージ', #例
                'required' => false,
                'attr'=>[
                        'placeholder'=>'Columnの場合イメージを入れてください'
                ],

            ])
            ->add('text03', ChoiceType::class, [
                'label' => 'カラム区分',#例
                'choices' => $this->Common->Yml('column_kbn',true),
                'required' => true,
                'placeholder'=>'Columnの場合選択してください'

            ])
*/
    ;}


    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'Admin_SubContent_Edit';
    }

}
