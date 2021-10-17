<?php
namespace Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Guysolamour\Administrable\Facades\Cart;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class CommandController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $commands = config('administrable.extensions.shop.models.command')::online()->last()->get();

        return back_view('extensions.shop.commands.index', compact('commands'));
    }

    public function create()
    {
        $command  = new (config('administrable.extensions.shop.models.command'));

        $products = config('administrable.extensions.shop.models.product')::with('media')->principal()->last()->get()->each->append(['gallery']);
        $clients  = User::all();
        $states   = $command->getStates();
        $delivers = config('administrable.extensions.shop.models.deliver')::with('areas')->get();

        return back_view('extensions.shop.commands.create', compact(
            'command',
            'products',
            'clients',
            'states',
            'delivers'
        ));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'command_state'  => 'required|string',
            'client'         => 'required|array',
            'deliver'        => 'required|array',
            'products'       => 'required',
            'globals'        => 'required',
            'created_at'     => 'required|string',
        ]);

        if ($validator->fails()) {
            flashy()->error("Erreur lors de la validation du formulaire");
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $deliver  = config('administrable.extensions.shop.models.deliver')::where('id', $request->input('deliver.id'))->with([
            'areas' => fn ($query) => $query->where('id', $request->input('deliver.area')),
        ])->first();

        $products = array_map(function ($item) {
            return [
                'rowId'    => $item['product']['id'],
                'name'     => null,
                'model'    => config('administrable.extensions.shop.models.product'),
                'image'    => $item['product']['gallery']['front']['url'],
                'price'    => $item['product']['current_price'],
                'quantity' => $item['quantity'] ?? 1,
                'tax'      => $item['tax'] ?? 0,
                'discount' => $item['discount'] ?? 0,
            ];
        }, json_decode($request->products, true));


        $data = [
            'state'          => $request->input('command_state'),
            'user_id'        => $request->input('client')['id'] ?: null,
            'name'           => $request->input('client')['name'],
            'email'          => $request->input('client')['email'],
            'address'        => $request->input('deliver')['address'],
            'phone_number'   => $request->input('client')['phone_number'],
            'city'           => $request->input('client')['city'],
            'country'        => $request->input('client')['country'],
            'created_at'     => parse_range_dates($request->input('created_at')),
            'online'         => $request->input('online', true),
            'products'       => $products,
            'globals'        => json_decode($request->globals, true),

        ];

        if ($request->input('deliver.id') && $request->deliver('deliver.area')) {
            $data['deliver'] = [
                'deliver_id' => $request->input('deliver.id'),
                'area_id'    => $request->input('deliver.area'),
                'price'      => (int) $deliver->areas->first()->pivot->price,
            ];
        }
        /**
         * @var \Guysolamour\Administrable\Models\Extensions\Shop\Command
         */
        $command = config('administrable.extensions.shop.models.command')::create($data);

        // save notes
        $notes = array_map(function ($note) use ($command) {
            return [
                'commenter_id'     => $note['commenter']['id'],
                'commenter_type'   => get_class(get_guard()),
                'commentable_id'   => $command->id,
                'commentable_type' => $note['commentable_type'],
                'comment'          => $note['comment'],
            ];
        }, json_decode($request->input('notes'), true));

        $command->notes()->createMany($notes);

        flashy("La commande a été bien ajoutée");

        return redirect_backroute('extensions.shop.command.index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $command = config('administrable.extensions.shop.models.command')::where('id', $id)->firstOrFail();

        $command->load('client');

        $products = config('administrable.extensions.shop.models.product')::principal()->last()->get();

        $clients = User::all();

        $states   = $command->getStates();

        $notes = $command->notes->each->load('author');

        $delivers = config('administrable.extensions.shop.models.deliver')::with('areas')->get();

        return back_view('extensions.shop.commands.edit', compact(
            'command',
            'products',
            'clients',
            'delivers',
            'states',
            'notes'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $command = config('administrable.extensions.shop.models.command')::where('id', $id)->firstOrFail();

        if ($command->isPaid()) {
            flashy()->error("Cette commande a été payée, vous ne devriez plus la modifier.");
            return back();
        }

        $validator = Validator::make($request->all(), [
            'command_state'  => 'required|string',
            'client'         => 'required|array',
            'deliver'        => 'required|array',
            'created_at'     => 'required|string',
        ]);

        if ($validator->fails()) {
            flashy()->error("Erreur lors de la validation du formulaire");
            return back()->withErrors($validator)->withInput();
        }

        $command->update([
            'state'         => $request->input('command_state'),
            'name'          => $request->input('client')['name'],
            'email'         => $request->input('client')['email'],
            'address'       => $request->input('deliver')['address'],
            'phone_number'  => $request->input('client')['phone_number'],
            'city'          => $request->input('client')['city'],
            'country'       => $request->input('client')['country'],
            'created_at'    => parse_range_dates($request->input('created_at')),
            'online'        => $request->input('online', true),
            'user_id'       => $request->input('client')['id'] ?: null,
        ]);

        flashy('La commande a bien été enregistré');

        return back();
    }


    public function confirmPayment(int $id)
    {
        $command = config('administrable.extensions.shop.models.command')::where('id', $id)->firstOrFail();
        $command->confirmPayment();

        flashy("Le paiement de la commande a bien été confirmé.");

        return back();
    }


    public function applyDiscount(Request $request, int $id)
    {
        /**
         * @var \Guysolamour\Administrable\Models\Extensions\Shop\Command
         */
        $command = config('administrable.extensions.shop.models.command')::where('id', $id)->firstOrFail();

        $cart =  $command->applyDiscount($request->input('discount', 0));

        return response()->json(['cart' => $cart]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $command = config('administrable.extensions.shop.models.command')::where('id', $id)->firstOrFail();
        $command->delete();

        flashy('L\' élément a bien été supprimé');

        return redirect_backroute('extensions.shop.command.index');
    }

    /**
     *
     * @param int $command
     * @param Request $request
     * @return array
     */
    public function addProduct(int $id, Request $request)
    {
        $command = config('administrable.extensions.shop.models.command')::where('id', $id)->firstOrFail();

        $product = config('administrable.extensions.shop.models.product')::findOrFail($request->get('rowId'));

        $products = $command->addProductsItem($product);

        return response()->json(['cart' => Cart::hydrate($products)]);
    }

    /**
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeProduct(int $id, Request $request)
    {
        $command = config('administrable.extensions.shop.models.command')::where('id', $id)->firstOrFail();

        $command->removeProductsItem($request->get('rowId'));

        return response()->json(['cart' => Cart::hydrate($command->products, $command->globals)]);
    }

    /**
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProduct(int $id, Request $request)
    {
        $command = config('administrable.extensions.shop.models.command')::where('id', $id)->firstOrFail();

        $command->updateProductsItem($request->input('rowId'), 'quantity', (int) $request->input('quantity'));

        return response()->json(['cart' => Cart::hydrate($command->products, $command->globals)]);
    }

    public function statistic()
    {
        $orders = config('administrable.extensions.shop.models.order')::with('command')->get();

        $current_month_amount = $orders->filter(fn ($item) => $item->created_at->isCurrentMonth() && $item->created_at->isCurrentYear())->sum('amount');
        $current_year_amount  = $orders->filter(fn ($item) => $item->created_at->year == now()->year)->sum('amount');
        $total_orders         = $orders->sum('amount');

        $users_sorted_by_expense = User::sortByTotalExpense(10);

        $most_sales_products = config('administrable.extensions.shop.models.product')::mostSales();

        $sold_out_products = config('administrable.extensions.shop.models.product')::soldOut()->limit(10)->get();


        return back_view("extensions.shop.statistic.index", compact(
            'orders',
            'current_month_amount',
            'current_year_amount',
            'total_orders',
            'users_sorted_by_expense',
            'most_sales_products',
            'sold_out_products'
        ));
    }
}
