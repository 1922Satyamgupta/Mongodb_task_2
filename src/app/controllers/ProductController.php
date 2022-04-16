<?php

use Phalcon\Mvc\Controller;


class ProductController extends Controller
{
    public function indexAction()
    {
        
        
        
    }
    public function registerAction()
    {
        
        $collection = $this->mongo->demo->beers;
        $name = $this->request->get('p_name');
        $category = $this->request->get('cat_name');
        $price = $this->request->get('price');
        $stock = $this->request->get('stock');
        $label = $this->request->get('label');
        $value = $this->request->get('value');
        $attr_name = $this->request->get('attr_name');
        $attr_value = $this->request->get('attr_value');
        $arr = [];
        for ($i = 0; $i < count($label); $i++) {
            $arr2['label'] = $label[$i];
            $arr2['value'] = $value[$i];
            array_push($arr, $arr2);
        }
        $var = [];
        for ($i = 0; $i < count($attr_name); $i++) {
            $arr3['attr_name'] = $attr_name[$i];
            $arr3['attr_value'] = $attr_value[$i];
            array_push($var, $arr3);
        }
        $result = $collection->insertOne(['p_name' => $name, 'cat_name' => $category, 'price' => $price, 'stock' => $stock, 'metafield' => $arr, 'variation' => $var] );
        $this->response->redirect('product/view');
        
    }
    public function viewAction()
    {
        $collection = $this->mongo->demo->beers;

        $result = $collection->find();
        if ($this->request->getPost('search')) {
            $productsearch = $this->request->getPost('p_name');

            foreach ($result as $k => $v) {
                if (strtolower($v->p_name) == strtolower($productsearch)) {
                    $this->view->products =  $v;
                }
            }
        }
        $this->view->products = $result;
    }
    public function deleteAction() {
       
       $id = $this->request->getQuery('id');
        $collection = $this->mongo->demo->beers;
        $save = $collection->deleteOne(["_id" => new \MongoDB\BSON\ObjectID($id)]);
        $this->response->redirect('product/view');
    }
    public function editAction() {
        $id = $this->request->getQuery('id');
        $collection = $this->mongo->demo->beers;
        $result = $collection->findOne(["_id" => new \MongoDB\BSON\ObjectID($id)]);
        $this->view->edit_data = $result;
        $result->p_name = $this->request->get('name');
        $result->cat_name = $this->request->get('cat_name');
        $result->price = $this->request->get('price');
        $result->stock = $this->request->get('stock');
        for($p =0 ; $p <count($result->metafield) ; $p++) {
            $result->metafield[$p]["label"] = $this->request->get('label')[$p] ;
            $result->metafield[$p]["value"] = $this->request->get('value')[$p] ;
        }
        for($p =0 ; $p <count($result->variation) ; $p++) {
            $result->variation[$p]["attr_name"] = $this->request->get('attr_name')[$p] ;
            $result->variation[$p]["attr_value"] = $this->request->get('attr_value')[$p] ;
        }
        $save = $collection->updateOne(["_id" => new \MongoDB\BSON\ObjectID($id)], ['$set'=>$result]);
        // $this->response->redirect('product/view');
    }


}

  