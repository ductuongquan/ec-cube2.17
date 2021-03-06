<?php

class SC_CustomerListTest extends Common_TestCase
{
    /** @var Faker\Generator $faker */
    protected $faker;

    /** @var FixtureGenerator */
    protected $objGenerator;

    /** @var int[] */
    protected $customer_ids;

    /** @var array */
    protected $params = [];

    protected function setUp()
    {
        parent::setUp();
        $this->faker = Faker\Factory::create('ja_JP');
        $this->objGenerator = new FixtureGenerator();
    }

    public function testCreateInstance()
    {
        $this->setUpCustomers(1);
        $this->scenario();

        $this->assertCount(1, $this->actual);
    }

    public function testSearchCustomerId()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->params['search_customer_id'] = $this->customer_ids[0];

        $this->scenario();
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->verify();
    }

    public function testSearchName01()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_name'] = $this->expected[0]['name01'];

        $this->scenario();
        $this->assertEquals($this->expected[0]['name01'], $this->actual[0]['name01']);
    }

    public function testSearchName02()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_name'] = $this->expected[0]['name02'];

        $this->scenario();
        $this->assertEquals($this->expected[0]['name02'], $this->actual[0]['name02']);
    }

    public function testSearchKana01()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_kana'] = $this->expected[0]['kana01'];

        $this->scenario();
        $this->assertEquals($this->expected[0]['kana01'], $this->actual[0]['kana01']);
    }

    public function testSearchKana02()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_kana'] = $this->expected[0]['kana02'];

        $this->scenario();
        $this->assertEquals($this->expected[0]['kana02'], $this->actual[0]['kana02']);
    }

    public function testSearchPref()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_pref'] = $this->expected[0]['pref'];

        $this->scenario();
        $this->assertEquals($this->expected[0]['pref'], $this->actual[0]['pref']);
    }

    public function testSearchTel()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_tel'] = $this->expected[0]['tel01'].$this->expected[0]['tel02'].$this->expected[0]['tel03'];

        $this->scenario();
        $this->verify();
    }

    public function testSearchSex()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_sex'] = [$this->expected[0]['sex']];

        $this->scenario();
        $this->assertEquals($this->expected[0]['sex'], $this->actual[0]['sex']);
    }

    public function testSearchJob()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->expected = $this->objQuery->getRow('*', 'dtb_customer', 'customer_id = ?', [$this->customer_ids[0]]);

        $this->params['search_job'] = [$this->expected['job']];

        $this->scenario();
        // SC_CustomerList::getList() ??? job ?????????????????????????????????????????????
        $this->actual = $this->objQuery->getRow('*', 'dtb_customer', 'customer_id = ?', [$this->actual[0]['customer_id']]);
        $this->assertEquals($this->expected[0]['pref'], $this->actual[0]['pref']);
    }

    public function testSearchJobWithUnknown()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['job' => null], 'customer_id = ?', [$this->customer_ids[0]]);
        $this->expected = $this->objQuery->getRow('*', 'dtb_customer', 'customer_id = ?', [$this->customer_ids[0]]);

        $this->params['search_job'] = ['??????'];

        $this->scenario();
        // SC_CustomerList::getList() ??? job ?????????????????????????????????????????????
        $this->actual = $this->objQuery->getRow('*', 'dtb_customer', 'customer_id = ?', [$this->actual[0]['customer_id']]);
        $this->verify();
    }

    public function testSearchEmail()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_email'] = $this->expected[0]['email'].', '.$this->faker->safeEmail;

        $this->scenario();
        $this->assertEquals($this->expected[0]['email'], $this->actual[0]['email']);
    }

    public function testSearchEmailWithExclude()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_email'] = '111'.$this->faker->safeEmail;
        $this->params['not_emailinc'] = 1;

        $this->scenario();
        $this->assertCount(3, $this->actual);
    }

    public function testSearchEmailMobile()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_email_mobile'] = $this->expected[0]['email_mobile'].', '.$this->faker->safeEmail;

        $this->scenario();
        $this->assertEquals($this->expected[0]['email_mobile'], $this->actual[0]['email_mobile'], 'email_mobile ??????????????????????????? null ???????????????');
    }

    public function testSearchEmailMobileWithExclude()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_email'] = '111'.$this->faker->safeEmail;
        $this->params['not_email_mobileinc'] = 1;

        $this->scenario();
        $this->assertCount(0, $this->actual, 'email_mobile ??????????????????????????? 0');
    }

    public function testSearchHtmlmail1()
    {
        $this->objQuery->update('dtb_customer', ['mailmaga_flg' => 3]);
        $this->customer_ids = $this->setUpCustomers(3);
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_htmlmail'] = '99'; // '?????????????????????????????????????????????????????????'

        $this->scenario();
        $this->assertCount(3, $this->actual, '99 ?????????????????????');
    }

    public function testSearchHtmlmail2()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['mailmaga_flg' => 3]);
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_htmlmail'] = ''; // HTML+TEXT

        $this->scenario();
        $this->assertCount(0, $this->actual, 'mailmaga_flg = 3 ???????????????');
    }

    public function testSearchHtmlmail3()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['mailmaga_flg' => 1], 'customer_id = ?', [$this->customer_ids[0]]);
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_htmlmail'] = '1'; // HTML

        $this->scenario();
        $this->assertEquals($this->expected[0]['mailmaga_flg'], $this->actual[0]['mailmaga_flg']);
    }

    public function testSearchHtmlmail4()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['mailmaga_flg' => 2], 'customer_id = ?', [$this->customer_ids[0]]);
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_htmlmail'] = '2'; // TEXT

        $this->scenario();
        $this->assertEquals($this->expected[0]['mailmaga_flg'], $this->actual[0]['mailmaga_flg']);
    }

    public function testSearchMailType1()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', [], '', [], ['email_mobile' => 'email']);
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_mail_type'] = 1; // PC?????????????????????

        $this->scenario();
        $this->assertCount(0, $this->actual, 'PC?????????????????????????????????????????????????????????????????????????????????????????????');
    }

    public function testSearchMailType2()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_mail_type'] = 2; // ???????????????????????????

        $this->scenario();
        $this->assertCount(0, $this->actual, '??????????????????????????????????????????????????????????????????');
    }

    public function testSearchMailType3()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['email_mobile' => $this->faker->safeEmail]);
        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_mail_type'] = 3; // PC????????????????????? (???????????????????????????????????????????????????????????????)

        $this->scenario();
        $this->assertCount(0, $this->actual, '???????????????????????????????????????????????????????????????');
    }

    public function testSearchMailType4()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', [], '', [], ['email_mobile' => 'email']);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_mail_type'] = 4; // PC????????????????????? (???????????????????????????????????????????????????????????????)

        $this->scenario();
        $this->assertCount(3, $this->actual, 'email ??? email_mobile ????????????????????????????????????');
    }

    public function testSearchBuyTotalFrom()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['buy_total' => 10000], 'customer_id = ?', [$this->customer_ids[0]]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_buy_total_from'] = 10000;

        $this->scenario();
        $this->verify();
    }

    public function testSearchBuyTotalTo()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['buy_total' => 10001], 'customer_id <> ?', [$this->customer_ids[0]]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_buy_total_to'] = 10000;

        $this->scenario();
        $this->assertCount(1, $this->actual);
    }

    public function testSearchBuyTotalFromTo()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['buy_total' => 10001], 'customer_id = ?', [$this->customer_ids[0]]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_buy_total_from'] = 9999;
        $this->params['search_buy_total_to'] = 10001;

        $this->scenario();
        $this->assertCount(1, $this->actual);
    }

    public function testSearchBuyTimesFrom()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['buy_times' => 10000], 'customer_id = ?', [$this->customer_ids[0]]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_buy_times_from'] = 10000;

        $this->scenario();
        $this->verify();
    }

    public function testSearchBuyTimesTo()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['buy_times' => 10001], 'customer_id <> ?', [$this->customer_ids[0]]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_buy_times_to'] = 10000;

        $this->scenario();
        $this->assertCount(1, $this->actual);
    }

    public function testSearchBuyTimesFromTo()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['buy_times' => 10001], 'customer_id = ?', [$this->customer_ids[0]]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_buy_times_from'] = 9999;
        $this->params['search_buy_times_to'] = 10001;

        $this->scenario();
        $this->assertCount(1, $this->actual);
    }

    public function testSearchBirthFrom()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['birth' => '2030-01-01'], 'customer_id = ?', [$this->customer_ids[0]]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_b_start_year'] = '2030';
        $this->params['search_b_start_month'] = '01';
        $this->params['search_b_start_day'] = '01';

        $this->scenario();
        $this->verify();
    }

    public function testSearchBirthTo()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['birth' => '1970-01-01'], 'customer_id = ?', [$this->customer_ids[0]]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_b_end_year'] = '1970';
        $this->params['search_b_end_month'] = '01';
        $this->params['search_b_end_day'] = '01';

        $this->scenario();
        $this->verify();
    }

    public function testSearchBirthFromTo()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['birth' => '1970-01-02'], 'customer_id = ?', [$this->customer_ids[0]]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_b_start_year'] = '1970';
        $this->params['search_b_start_month'] = '01';
        $this->params['search_b_start_day'] = '01';
        $this->params['search_b_end_year'] = '1970';
        $this->params['search_b_end_month'] = '01';
        $this->params['search_b_end_day'] = '02';

        $this->scenario();
        $this->verify();
    }

    public function testSearchUpdateDateFrom()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['update_date' => '2030-01-01'], 'customer_id = ?', [$this->customer_ids[0]]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_start_year'] = '2030';
        $this->params['search_start_month'] = '01';
        $this->params['search_start_day'] = '01';

        $this->scenario();
        $this->verify();
    }

    public function testSearchUpdateDateTo()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['update_date' => '1970-01-01'], 'customer_id = ?', [$this->customer_ids[0]]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_end_year'] = '1970';
        $this->params['search_end_month'] = '01';
        $this->params['search_end_day'] = '01';

        $this->scenario();
        $this->verify();
    }

    public function testSearchUpdateDateFromTo()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['update_date' => '1970-01-02'], 'customer_id = ?', [$this->customer_ids[0]]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_start_year'] = '1970';
        $this->params['search_start_month'] = '01';
        $this->params['search_start_day'] = '01';
        $this->params['search_end_year'] = '1970';
        $this->params['search_end_month'] = '01';
        $this->params['search_end_day'] = '02';

        $this->scenario();
        $this->verify();
    }

    public function testSearchBuyStartDateFrom()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['last_buy_date' => '2030-01-01'], 'customer_id = ?', [$this->customer_ids[0]]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_buy_start_year'] = '2030';
        $this->params['search_buy_start_month'] = '01';
        $this->params['search_buy_start_day'] = '01';

        $this->scenario();
        $this->verify();
    }

    public function testSearchBuyStartDateTo()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['last_buy_date' => '1970-01-01'], 'customer_id = ?', [$this->customer_ids[0]]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_buy_end_year'] = '1970';
        $this->params['search_buy_end_month'] = '01';
        $this->params['search_buy_end_day'] = '01';

        $this->scenario();
        $this->verify();
    }

    public function testSearchBuyStartDateFromTo()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['last_buy_date' => '1970-01-02'], 'customer_id = ?', [$this->customer_ids[0]]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_buy_start_year'] = '1970';
        $this->params['search_buy_start_month'] = '01';
        $this->params['search_buy_start_day'] = '01';
        $this->params['search_buy_end_year'] = '1970';
        $this->params['search_buy_end_month'] = '01';
        $this->params['search_buy_end_day'] = '02';

        $this->scenario();
        $this->verify();
    }

    public function testSearchBuyProductCode()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $order_id = $this->objGenerator->createOrder($this->customer_ids[0], [1]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_buy_product_code'] = 'ice-01';

        $this->scenario();
        $this->verify();
    }

    public function testSearchBuyProductName()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $order_id = $this->objGenerator->createOrder($this->customer_ids[0], [1]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_buy_product_name'] = '?????????';

        $this->scenario();
        $this->verify();
    }

    public function testSearchStatus()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $this->objQuery->update('dtb_customer', ['status' => '1'], 'customer_id = ?', [$this->customer_ids[0]]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_status'] = [1];

        $this->scenario();
        $this->verify();
    }

    public function testSearchBuyCategory()
    {
        $this->customer_ids = $this->setUpCustomers(3);
        $order_id = $this->objGenerator->createOrder($this->customer_ids[0], [1]);

        $this->expected = $this->getCustomerList([$this->customer_ids[0]]);
        $this->params['search_category_id'] = 5; // ????????????????????????????????????

        $this->scenario();
        $this->verify();
    }

    protected function scenario()
    {
        $objSelect = new SC_CustomerList_Ex($this->params, 'customer');
        $this->actual = $this->objQuery->getAll($objSelect->getList(), $objSelect->arrVal);
    }

    /**
     * ????????????????????????????????????.
     *
     * @param int[] $customer_ids customer_id ?????????
     */
    protected function getCustomerList($customer_ids)
    {
        return $this->objQuery->getAll('SELECT customer_id,name01,name02,kana01,kana02,sex,email,email_mobile,tel01,tel02,tel03,pref,status,update_date,mailmaga_flg FROM dtb_customer WHERE customer_id IN ('.SC_Utils_Ex::repeatStrWithSeparator('?', count($customer_ids), ',').')', $customer_ids);
    }
    /**
     * @param int $n ?????????
     * @return int[] customer_id ?????????
     */
    protected function setUpCustomers($n)
    {
        $this->objQuery->delete('dtb_customer');
        $result = [];
        for ($i = 0; $i < $n; $i++) {
            $result[] = $this->objGenerator->createCustomer();
        }
        return $result;
    }
}
