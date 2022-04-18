<?php

require '../vendor/autoload.php';

use Phalcon\Mvc\Controller;



class OrderController extends Controller
{

    public function indexAction()
    {
        if (isset($_POST['submit'])) {
            print_r($_POST);
            die;
        }
        $collection = $this->mongo->demo->beers;
        $cursor = $collection->find();
        $this->view->data = $cursor;
    }
    public function registerAction()
    {

        $collection = $this->mongo->demo->order;
        $pro_name = $this->request->get('variation');

        $variate = $this->request->get('variate');

        $cust_name = $this->request->get('cust_name');
        $quanti = $this->request->get('quanti');
        $address = $this->request->get('address');
        $email = $this->request->get('email');
        $price = $this->request->get('priceTo');
        $order_status = 'paid';
        $total_price = $price * $quanti;

        $contact_no = $this->request->get('contact_no');
        $date = date("d.m.y");
        $result = $collection->insertOne(['pro_name' => $pro_name, 'variate' => $variate, 'address' => $address, 'cust_name' => $cust_name, 'quanti' => $quanti, 'email' => $email, 'contact' => $contact_no, 'Total_price' => $total_price, 'order_status' => $order_status, 'date' => $date]);

        $this->response->redirect('order/view');
    }
    public function ajaxAction()
    {
        $id = $_POST['cid'];
        $collection = $this->mongo->demo->beers;
        $cursor = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        echo ' <div class="form-group">
        <label class="col-md-4 control-label">Select Variation</label>
        <div class="col-md-4 inputGroupContainer">
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>';
        echo "<input type='hidden' name = 'priceTo' value = '" . $cursor['price'] . "' >";
        echo "<br><select name = 'variate'>";
        foreach ($cursor['metafield'] as $p) {
            echo ('<option >' . $p['label'] . ' ' . $p['value'] . '</option>');
        }
        echo "</select>";
        echo '  </div>
                    </div>
                </div>';


        die;
    }
    public function viewAction()
    {
        $collection = $this->mongo->demo->order;

        $result = $collection->find();
        $this->view->order = $result;
        if ($this->request->getPost('status')) {
            $order_id = $this->request->getPost('order_id');
            $status = $this->request->getPost('status');
            $order_status = array(
                "order_status" => $status
            );

            $this->mongo->demo->order->updateOne(["_id" => new MongoDB\BSON\ObjectID($order_id)], ['$set' => $order_status]);
        }
        if ($this->request->getPost('filter_status')) {
            $status_choose = $this->request->getPost('filter_status');
            // print_r($status);
            // die;
            $filter_status = array(
                "order_status" => $status_choose
            );
            $filter_by_status = $this->mongo->orders->find($filter_status);
            $this->view->order = $filter_by_status;
        }

        if ($this->request->getPost('filter_date')) {
            $date = $this->request->getPost('filter_date');

            if ($date == 'today') {
                $filter_date = array(
                    "order_date" => date('d/m/y')
                );
                $filter_by_date = $this->mongo->orders->find($filter_date);
                $this->view->order = $filter_by_date;
            }
            if ($date == "this_week") {
                $start_date = date("d/m/y", strtotime("-1 week"));
                $end_date = date("d/m/y");
                $orders = array('order_date' => ['$gte' => $start_date, '$lte' => $end_date]);
                $orders = $this->mongo->orders->find($orders);
                $this->view->order = $orders;
            }
        } else {
            $this->view->order = $data;
        }
    }
}
