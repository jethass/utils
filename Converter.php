abstract class AbstractConverter
{

    protected $matches;

    /**
     * @param array $input
     * @return array
     */
    public function convert(array $input)
    {
        $data = array();

        foreach ($input as $key => $value) {

            if (false !== ($keyMatches = array_search($key, $this->matches))) {
                $data[$keyMatches] = $value;
            }
        }

        return $data;
    }
}

class CustomerConverter extends AbstractConverter
{

    protected $matches = array(
        'lastname' => 'nom',
        'zipcode' => 'codepostal',
        'phoneNumber' => 'telephone',
        'firstname' => 'prenom',
        'civility' => 'civilite',
        'email' => 'email',
        'cmpid' => 'cmpid'
    );

}


 $parameters   = $request->query->all();
 $customerConverterData = (new CustomerConverter())->convert($parameters);
