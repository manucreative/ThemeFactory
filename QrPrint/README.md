
# Understanding PrintComponent.js

The PrintComponent.js file defines a React component that is responsible for rendering a printable receipt for a Point of Sale (POS) system. The component receives data about the transaction and the items purchased as props, and uses this data to render a formatted receipt that can be printed out.

## breakdown of the key sections of the code:

1. Importing Dependencies: The file starts by importing various dependencies that the component needs, including React, PropTypes, and the moment library.
2. Defining the PrintComponent Component: The PrintComponent component is defined as a class that extends the React.Component base class. The component defines a render method that returns the JSX markup for the printable receipt.
3. Formatting the Date and Time: The component uses the moment library to format the transaction date and time into a human-readable format.
4. Rendering the Header: The component defines a renderHeader method that returns the JSX markup for the receipt header. The header includes the store name and address, as well as the transaction date and time.
5. Rendering the Items: The component defines a renderItems method that returns the JSX markup for the list of purchased items. The method loops over the items prop and renders each item's name, quantity, and price.
6. Rendering the Totals: The component defines a renderTotals method that returns the JSX markup for the transaction totals. The method calculates the subtotal, tax, and total based on the items prop and renders them in the receipt.
7. Rendering the Footer: The component defines a renderFooter method that returns the JSX markup for the receipt footer. The footer includes a thank-you message and a prompt for the customer to provide feedback.
8. Rendering the Printable Receipt: Finally, the render method combines the various sections of the receipt (header, items, totals, and footer) into a single JSX element that can be printed out.

## Data Flow Diagram for PrintComponent.js

```
+------------------------+
|  Transaction and Items  |
|        (props)          |
+------------------------+
              |
              v
  +--------------------+
  | PrintComponent.js  |
  |   (React Component)|
  +--------------------+
              |
              v
       +----------+
       | Printable|
       | Receipt  |
       +----------+
```

## How to modify the footer

Open the PrintComponent.js file and follow the instrutions:

### OVerView Explanation

1. Fetch the Invoice Data: The first step is to fetch the relevant invoice data from the Magento 2 database. You can use the Magento 2 REST API to retrieve the invoice data based on the transaction ID. The API endpoint you need to use is /rest/V1/invoices/:id, where :id is the ID of the invoice that corresponds to the transaction.

2. Retrieve the Square Image Name: Once you have the invoice data, you can retrieve the name of the square image from the invoice table. You will need to look for the square_image_name attribute in the extension_attributes object of the invoice data.

3. Retrieve the Control Unit Number: Similarly, you can retrieve the control unit number from the invoice data. This information is stored in the merchant_reference attribute of the invoice data.

4. Retrieve the Control Unit Invoice Number: Finally, you can retrieve the control unit invoice number from the invoice data. This information is stored in the increment_id attribute of the invoice data.

5. Add the New Functionality to the Footer: Once you have retrieved the necessary data, you can modify the renderFooter method to add the new functionality to the footer. You can add a new <div> element to the footer and include the square image, control unit number, and control unit invoice number in this element.

Here's an example of how you can modify the renderFooter method to add the new functionality:

```javascript
renderFooter() {
  const { invoice } = this.props;

  // Retrieve the square image name, control unit number, and control unit invoice number from the invoice data
  const squareImageName = invoice.extension_attributes.square_image_name;
  const controlUnitNumber = invoice.merchant_reference;
  const controlUnitInvoiceNumber = invoice.increment_id;

  // Add the new functionality to the footer
  return (
    <div className="footer">
      <p>Thank you for shopping with us!</p>
      <p>Please provide your feedback at feedback@example.com</p>
      <img src={squareImageName} alt="Square Image" />
      <p>Control Unit Number: {controlUnitNumber}</p>
      <p>Control Unit Invoice Number: {controlUnitInvoiceNumber}</p>
    </div>
  );
}
```

Note that you will need to make sure that the invoice prop is passed to the PrintComponent component, and that the necessary invoice data is included in this prop.

### Detailed Break Down of Implementation: Magento 2 Extension

1. Create a New Magento 2 Extension: 

To add the necessary functionality, you'll need to create a new Magento 2 extension. You can create this extension using the bin/magento command-line tool. Here's an example command that creates a new extension called MyCompany_PosExtension in the app/code directory. You can use the command below if you already are familiar with the normal process of creating a magento extension and are looking for a quicker way to execute.

```lua
bin/magento module:create MyCompany_PosExtension --namespace=MyCompany --path=app/code
```
This command will create a new directory called MyCompany_PosExtension in the app/code directory, with the necessary files and directories for a Magento 2 extension.

2. Create a New Controller:

Next, you'll need to create a new controller that handles the API request to retrieve the invoice data. You can create this controller in the Controller/Index directory of your extension. Here's an example controller that retrieves the invoice data based on the transaction ID. Here's a code template to guide you.

```php
<?php

namespace MyCompany\PosExtension\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use [Vendor]\[Module]\Api\InvoiceInterface;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Api\InvoiceRepositoryInterface;

class GetInvoiceData extends Action
{
    private $invoiceRepository;
    private $resultJsonFactory;
    protected $invoiceInterface;

    public function __construct(
        Context $context,
        InvoiceRepositoryInterface $invoiceRepository,
         InvoiceInterface $invoiceInterface,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->invoiceRepository = $invoiceRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->invoiceInterface = $invoiceInterface;
    }

    public function execute()
    {
        $transactionId = $this->getRequest()->getParam('transaction_id');
        $invoiceData = $this->invoiceInterface->getInvoiceData($invoiceId);
        // $invoice = $this->invoiceRepository->getByTransactionId($transactionId);
        $invoiceData = $invoice->getData();
        $result = $this->resultJsonFactory->create();
        return $result->setData($invoiceData);
    }
}
```
This controller retrieves the transaction ID from the API request parameters, fetches the invoice data from the Magento 2 database using the InvoiceRepositoryInterface, and returns the invoice data as a JSON response.

This controller retrieves the transaction ID from the API request parameters, fetches the invoice data from the Magento 2 database using the InvoiceRepositoryInterface, and returns the invoice data as a JSON response.

3. Create a New API Route:

To expose the invoice data through the Magento 2 REST API, you'll need to create a new API route. You can add this route in the etc/webapi.xml file of your extension. Here's an example webapi.xml file that adds a new API route for retrieving the invoice data. Here is the code template to guide you:

```xml
<?xml version="1.0"?>
<routes xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Webapi:etc/webapi.xsd">
    <route url="/V1/invoice/:id" method="GET">
        <service class="MyCompany\PosExtension\Api\InvoiceInterface" method="get"/>
        <resources>
            <resource ref="anonymous"/>
        </resources>
    </route>
</routes>
```

This webapi.xml file defines a new API route with the URL /V1/invoice/:id, where :id is the ID of the invoice that corresponds to the transaction. The service attribute specifies the API service class that handles the API request, and the resources element specifies that this API route can be accessed anonymously.

4. Create an InvoiceInterface API Service:

To handle the API request for retrieving the invoice data, you'll need to create a new API service. You can create this service in the Api directory

  1. Create a new file called InvoiceData.php in the directory app/code/{Vendor}/{Module}/Api/Data:

  ```php
  <?php

  namespace {Vendor}\{Module}\Api\Data;

  interface InvoiceDataInterface
  {
      /**
       * Get invoice data
       *
       * @param int $invoiceId
       * @return \Magento\Sales\Api\Data\InvoiceInterface
       * @throws \Magento\Framework\Exception\NoSuchEntityException If invoice with the specified ID does not exist.
       */
      public function getInvoiceData($invoiceId);
  }
  ```
  2. Create a new file called InvoiceRepository.php in the directory app/code/{Vendor}/{Module}/Api:

  ```php
  <?php

  namespace {Vendor}\{Module}\Api;

  use {Vendor}\{Module}\Api\Data\InvoiceDataInterface;
  use Magento\Framework\Api\SearchCriteriaBuilder;
  use Magento\Sales\Api\Data\InvoiceInterface;
  use Magento\Sales\Api\InvoiceRepositoryInterface;

  class InvoiceRepository implements InvoiceDataInterface
  {
      /**
       * @var InvoiceRepositoryInterface
       */
      protected $invoiceRepository;

      /**
       * @var SearchCriteriaBuilder
       */
      protected $searchCriteriaBuilder;

      /**
       * InvoiceRepository constructor.
       * @param InvoiceRepositoryInterface $invoiceRepository
       * @param SearchCriteriaBuilder $searchCriteriaBuilder
       */
      public function __construct(
          InvoiceRepositoryInterface $invoiceRepository,
          SearchCriteriaBuilder $searchCriteriaBuilder
      ) {
          $this->invoiceRepository = $invoiceRepository;
          $this->searchCriteriaBuilder = $searchCriteriaBuilder;
      }

      /**
       * @inheritDoc
       */
      public function getInvoiceData($invoiceId)
      {
          $searchCriteria = $this->searchCriteriaBuilder->addFilter('entity_id', $invoiceId)->create();
          $invoices = $this->invoiceRepository->getList($searchCriteria);
          $invoiceItems = $invoices->getItems();

          if (count($invoiceItems) == 0) {
              throw new \Magento\Framework\Exception\NoSuchEntityException(
                  __('Could not find invoice with ID: %1', $invoiceId)
              );
          }

          return reset($invoiceItems);
      }
  }
  ```

  3. Add the di.xml file to the app/code/{Vendor}/{Module}/etc directory to map the InvoiceDataInterface to the InvoiceRepository implementation:

    ```xml
    <?xml version="1.0"?>
    <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
        <preference for="{Vendor}\{Module}\Api\Data\InvoiceDataInterface" type="{Vendor}\{Module}\Api\InvoiceRepository" />
    </config>
  ```
## To test the working of this magento extension use the following details:

  1. Open Postman and create a new request.
  2. Set the request method to "GET".
  3. In the URL field, enter the endpoint URL for the API call. This will be the URL of your Magento 2 instance followed by the route to the custom API endpoint you created. For example, if your Magento 2 instance is running at "http://localhost/magento2" and your API endpoint route is "customapi/invoice", the URL would be "http://localhost/magento2/rest/V1/customapi/invoice".
  4. Click the "Headers" tab and add a new header with the key "Authorization" and the value "Bearer {{access_token}}". Replace "{{access_token}}" with the actual access token you obtained earlier.
  5. Click the "Send" button to send the request to the API endpoint.

You should receive a response containing the invoice data in JSON format. You can use this response to verify that the API call is working correctly.

### Detailed Break Down of Implementation: React Front End

 - First, you will need to modify the PrintComponent to store the additional information you want to display in its state. You can do this by adding the following code to the constructor method of the component:

 ```javascript
 this.state = {
  squareImage: '',
  controlUnitNumber: '',
  controlUnitInvoiceNumber: '',
};
```
Then, you will need to modify the componentDidMount method to fetch the additional information from the Magento 2 invoice table and update the component's state with the new values. Here's an example of how you could fetch the information:

```javascript
componentDidMount() {
  const invoiceId = this.props.order.invoice_id;
  fetch(`/get-invoice-data?id=${invoiceId}`)
    .then(response => response.json())
    .then(data => {
      this.setState({
        squareImage: data.square_image,
        controlUnitNumber: data.control_unit_number,
        controlUnitInvoiceNumber: data.control_unit_invoice_number,
      });
    })
    .catch(error => {
      console.error('Error fetching invoice data:', error);
    });
}
```

This assumes that you have created a new API endpoint in your Magento 2 component to fetch the additional invoice data. The endpoint should accept an id parameter (the invoice ID) and return a JSON response with the additional information you want to display.

Once you have the additional information in the component's state, you can update the render method to display it in the footer of the receipt. Here's an example of how you could modify the existing render method to display the new information:

```javascript
render() {
  const { order } = this.props;
  const { squareImage, controlUnitNumber, controlUnitInvoiceNumber } = this.state;
  return (
    <div className="receipt">
      <div className="receipt-header">
        <img src="/logo.png" alt="Logo" />
      </div>
      <div className="receipt-body">
        { /* Existing receipt body code here */ }
      </div>
      <div className="receipt-footer">
        {squareImage && <img src={`/images/${squareImage}`} alt="Square Image" />}
        {controlUnitNumber && <div className="control-unit-number">Control Unit Number: {controlUnitNumber}</div>}
        {controlUnitInvoiceNumber && <div className="control-unit-invoice-number">Control Unit Invoice Number: {controlUnitInvoiceNumber}</div>}
      </div>
    </div>
  );
}
```

This will display the square image, control unit number, and control unit invoice number in the footer of the receipt, if they are present in the component's state.

You will also need to create the new API endpoint in your Magento 2 component to fetch the additional invoice data, and update the Magento 2 database schema to include the new fields (square image, control unit number, and control unit invoice number) in the invoice table.

 - The Magento 2 side:

    - The file where you will add the necessary code to fetch the required information from the invoice table is app/code/Vendor/Module/Controller/Index/Print.php.
    - The file where you will define the necessary methods to fetch the required information from the invoice table is app/code/Vendor/Module/Model/Invoice.php.
    - The file where you will define the necessary database schema to store the image name, control unit number and control unit invoice number is app/code/Vendor/Module/Setup/InstallSchema.php.

 - The React side:

    - The file where you will add the necessary code to display the fetched information is client/pos/src/view/component/print/PrintComponent.js.

### Explanation:

The componentDidMount() method is already defined in the PrintComponent.js file, which is located at client/pos/src/view/component/print/PrintComponent.js.

To modify the method, you can open the PrintComponent.js file in your text editor or integrated development environment (IDE) and locate the componentDidMount() method within the PrintComponent class. You can then add the code to fetch the data you need from the Magento 2 invoice table and update the component's state with the fetched data using the setState() method.

Once you have updated the state with the fetched data, you can access the data in the state to render the new footer items in the render() method of the component.

---------------------

The componentDidMount() method is not explicitly referenced in the PrintComponent.js file since it is part of the React.Component class, which is already imported at the beginning of the file.

The PrintComponent class extends the React.Component class, which means it inherits all of the methods defined in the React.Component class, including the componentDidMount() method.

Therefore, when the PrintComponent is mounted or rendered for the first time, React will automatically call the componentDidMount() method defined in the React.Component class.

-------------------
