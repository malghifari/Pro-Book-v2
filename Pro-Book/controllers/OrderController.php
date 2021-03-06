<?php

require_once('./models/Book.php');
require_once('./models/User.php');
require_once('./models/Order.php');
require_once('./models/Review.php');
require_once('./controllers/Auth.php');
require_once('./libs/nusoap/nusoap.php');

class OrderController extends Controller
{
  private $clientSearch;
  private $clientRecommend;
  private $clientOrder;

  /**
   * Constructs UserController.
   *
   */
  public function __construct()
  {
    $this->clientSearch = new nusoap_client($_ENV['BOOK_WS_API'] . '/services/search?wsdl', 'wsdl');
    $this->clientOrder = new nusoap_client($_ENV['BOOK_WS_API'] . '/services/order?wsdl', 'wsdl');
    $this->clientRecommend = new nusoap_client($_ENV['BOOK_WS_API'] . '/services/recommendation?wsdl', 'wsdl');

    if ($this->clientSearch->getError() || $this->clientRecommend->getError() || $this->clientOrder->getError()){
      echo 'error!';
    }

    if (!Auth::check()){
      return $this->redirect('/index.php/login');
    }
  }

  /**
   * Redirect to index order page.
   *
   */
  public function index() {
    $order = new Order();

    if (Session::exist('isloginbygoogle')){
      $isSignedIn = Session::get('isloginbygoogle');
      if ($isSignedIn){
        $orders = $this->clientOrder->call('getOrderHistoryById', array('userId' => Session::get('google')['id']));

        return $this->view('history.php', [
          'username' => Session::get('google')['username'],
          'orders' => $orders
        ]);
      } else {
        return $this->redirect('/index.php/login', [
          'message' => 'Problem encountered'
        ]);
      }
    } else {
      $orders = $this->clientOrder->call('getOrderHistoryById', array('userId' => Auth::user()['id']));
      $orders = json_decode($orders);

      $books = array();

      $i = 0;
      foreach ($orders as $order) {
        $book = $this->clientSearch->call('getBookDetails', array('id' => $order->bookId));
        $books[$i] = json_decode($book);
        $i++;
      }

      return $this->view('history.php', [
        'orders' => $orders,
        'books' => $books,
        'username' => Auth::user()['username'],
      ]);
    }
  }

  /**
   * Redirect to order page.
   */
   public function create($request)
   {
     $id = $request['id'];

     if ($id == null){
       echo "Book id is not specified";
     } else {
       $result = $this->clientSearch->call('getBookDetails', array('id' => $id));

       $categories = json_decode($result)->volumeInfo->categories;
       $reviews = json_decode($result)->volumeInfo->reviews;

       $recommendedbooks = $this->clientRecommend->call('getRecommendedBooks', array('categories' => $categories[0]));
       $user = new User();
       $users = array();
       $i = 0;

       foreach ($reviews as $review) {
         $user = $user->find($review->userId);
         $users[$i] = $user;
         $i++;
       }

       if ($result == null){
         echo "Error, result null!";
       } else {
         $result = json_decode($result);
         $recommendedbooks = json_decode($recommendedbooks);

         return $this->view('order.php', [
           'book' => $result,
           'recommendedbooks' => $recommendedbooks,
           'reviews' => $reviews,
           'users' => $users,
           'username' => Auth::user()['username']
         ]);
       }
     }
   }
}
