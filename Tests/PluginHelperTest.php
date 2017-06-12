<?php

namespace My\Optimized\Tests;

use My\Optimized\Helpers\PluginHelper;
use My\Optimized\Models\PluginInfo;
use PHPUnit\Framework\TestCase;

class PluginHelperTest extends TestCase
{
	public function getPluginHelper() {
		$helper = new PluginHelper(array(
			'Plugin One' => new PluginInfo(array(
				'Name'    => 'Plugin One',
				'Version' => '0.5.0',
				'Active'  => false,
			)),
			'Plugin Two' => new PluginInfo(array(
				'Name'    => 'Plugin Two',
				'Version' => '1.5.4',
				'Active'  => true,
			)),
			'Plugin Three' => new PluginInfo(array(
				'Name'    => 'Plugin Three',
				'Version' => '1.0.0',
				'Active'  => true,
			)),
		));

		return $helper;
	}

	public function testHasPlugin() {
		$helper = $this->getPluginHelper();
		$this->assertTrue( $helper->hasPlugin( 'Plugin One' ) );
		$this->assertTrue( $helper->hasPlugin( 'Plugin Two' ) );
		$this->assertTrue( $helper->hasPlugin( 'Plugin Three' ) );
	}

	public function testDoesNotHavePlugin() {
		$helper = $this->getPluginHelper();
		$this->assertFalse( $helper->hasPlugin( 'Plugin Sixty' ) );
	}

	public function testGetAll() {
		$helper = $this->getPluginHelper();
		$this->assertCount( 3, $helper->getAll() );
	}

	public function testGetPluginInformation() {
		$helper    = $this->getPluginHelper();
		$pluginOne = $helper->getPluginInformation( 'Plugin One' );

		/**
		 * We just got Plugin One, and now we need to make sure that it is an
		 * instance of PluginInfo, and that the correct plugin was returned. */
		$this->assertTrue( $pluginOne instanceof PluginInfo );
		$this->assertEquals( 'Plugin One', $pluginOne->name );

		/**
		 * This is a non-existent plugin, and we need to make sure that it returns
		 * a null value. */
		$this->assertNull( $helper->getPluginInformation( 'Plugin Sixty' ) );
	}

	public function testVersionComparison() {
		$helper = $this->getPluginHelper();

		/**
		 * We are using Plugin Two as our test subject. The current version of Plugin Two is set to 1.5.4,
		 * and we are going to check whether Plugin Twos version is GT, GTE, EQ, LT, or LTE to the supplied
		 * 1.5.3 version number.
		 *
		 ** 1.5.4 is greater than or equal to 1.5.3 */
		$this->assertTrue( $helper->compareVersion( 'Plugin Two', '1.5.3', PluginHelper::COMPARE_GREATER_THAN_OR_EQUAL ) );

		/** 1.5.4 is greater than 1.5.3 */
		$this->assertTrue( $helper->compareVersion( 'Plugin Two', '1.5.3', PluginHelper::COMPARE_GREATER_THAN ) );

		/** 1.5.4 is not equal to 1.5.3 */
		$this->assertFalse( $helper->compareVersion( 'Plugin Two', '1.5.3', PluginHelper::COMPARE_EQUALS ) );

		/** 1.5.4 is not less than 1.5.3 */
		$this->assertFalse( $helper->compareVersion( 'Plugin Two', '1.5.3', PluginHelper::COMPARE_LESS_THAN ) );

		/** 1.5.4 is not less than or equal to 1.5.3 */
		$this->assertFalse( $helper->compareVersion( 'Plugin Two', '1.5.3', PluginHelper::COMPARE_LESS_THAN_OR_EQUAL ) );

		/**
		 * This time we are using Plugin Three as our test subject, which currently has its version set to 1.0.0.
		 * We are going to check whether Plugin Twos version is GT, GTE, EQ, LT, or LTE to the supplied 0.9.9
		 * version number.
		 *
		 ** 1.0.0 is greater than or equal to 0.9.9 */
		$this->assertTrue( $helper->compareVersion( 'Plugin Three', '0.9.9', PluginHelper::COMPARE_GREATER_THAN_OR_EQUAL ) );

		/** 1.0.0 is greater than 0.9.9 */
		$this->assertTrue( $helper->compareVersion( 'Plugin Three', '0.9.9', PluginHelper::COMPARE_GREATER_THAN ) );

		/** 1.0.0 is not equal to 0.9.9 */
		$this->assertFalse( $helper->compareVersion( 'Plugin Three', '0.9.9', PluginHelper::COMPARE_EQUALS ) );

		/** 1.0.0 is not less than 0.9.9 */
		$this->assertFalse( $helper->compareVersion( 'Plugin Three', '0.9.9', PluginHelper::COMPARE_LESS_THAN ) );

		/** 1.0.0 is not less than or equal to 0.9.9 */
		$this->assertFalse( $helper->compareVersion( 'Plugin Three', '0.9.9', PluginHelper::COMPARE_LESS_THAN_OR_EQUAL ) );

		/**
		 * This time we are using Plugin One as our test subject, which currently has its version set to 0.5.0.
		 * We are going to check whether Plugin Twos version is GT, GTE, EQ, LT, or LTE to the supplied 0.5.0
		 * version number.
		 *
		 ** 0.5.0 is greater than or equal to 0.5.0 */
		$this->assertTrue( $helper->compareVersion( 'Plugin Two', '0.5.0', PluginHelper::COMPARE_GREATER_THAN_OR_EQUAL ) );

		/** 0.5.0 is not greater than 0.5.0 */
		$this->assertFalse( $helper->compareVersion( 'Plugin Two', '0.5.0', PluginHelper::COMPARE_GREATER_THAN ) );

		/** 0.5.0 is equal to 0.5.0 */
		$this->assertTrue( $helper->compareVersion( 'Plugin Two', '0.5.0', PluginHelper::COMPARE_EQUALS ) );

		/** 0.5.0 is not less than 0.5.0 */
		$this->assertFalse( $helper->compareVersion( 'Plugin Two', '0.5.0', PluginHelper::COMPARE_LESS_THAN ) );

		/** 0.5.0 is less than or equal to 0.5.0 */
		$this->assertTrue( $helper->compareVersion( 'Plugin Two', '0.5.0', PluginHelper::COMPARE_LESS_THAN_OR_EQUAL ) );
	}

	public function testPluginIsActive() {
		$helper = $this->getPluginHelper();

		/**
		 * The first plugin shouldn't be active, and when a plugin isn't
		 * active, then it should return false. */
		$this->assertFalse( $helper->isPluginActive( 'Plugin One' ) );

		/**
		 * The next plugins (Two and Three) are active, and should return
		 * a positive result. */
		$this->assertTrue( $helper->isPluginActive( 'Plugin Two' ) );
		$this->assertTrue( $helper->isPluginActive( 'Plugin Three' ) );

		/** When a plugin doesn't exist, then it can't be acitvated. */
		$this->assertFalse( $helper->isPluginActive( 'Plugin Sixty' ) );
	}
}
