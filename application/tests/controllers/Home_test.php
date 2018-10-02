<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Welcome_test extends TestCase
{
	public function test_index()
	{
		$output = $this->request('GET', 'home/index');
		$this->assertContains('<title>Hospital Reservation System</title>', $output);
	}

	public function test_login()
	{
		$output = $this->request('GET', 'home/login');
		$this->assertContains('Login', $output);
	}

	public function test_signup()
	{
		$output = $this->request('GET', 'home/signup');
		$this->assertContains('Sign up', $output);
	}

	public function test_doctor()
	{
		$output = $this->request('GET', 'doctor/index');
		$this->assertEquals(NULL, $output);
	}

	public function test_sec()
	{
		$output = $this->request('GET', 'secretary/index');
		$this->assertEquals(NULL, $output);
	}

	public function test_patient()
	{
		$output = $this->request('GET', 'patient/index');
		$this->assertEquals(NULL, $output);
	}

	public function test_admin()
	{
		$output = $this->request('GET', 'admin/index');
		$this->assertEquals(NULL, $output);
	}

	public function test_doctor_appointments()
	{
		$output = $this->request('GET', 'doctor/appointments');
		$this->assertEquals(NULL, $output);
	}

	public function test_method_404()
	{
		$this->request('GET', 'home/method_not_exist');
		$this->assertResponseCode(404);
	}

	public function test_APPPATH()
	{
		$actual = realpath(APPPATH);
		$expected = realpath(__DIR__ . '/../..');
		$this->assertEquals(
			$expected,
			$actual,
			'Your APPPATH seems to be wrong. Check your $application_folder in tests/Bootstrap.php'
		);
	}
}
