<?php

use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_costs', function (Blueprint $table) {
            $table->increments("CS_ID");

//            $table->integer("CS_CODE");
            $table->integer("PKKEY")->nullable();
            $table->string("AirlineAndFlight")->nullable();
            $table->date("date_flight")->nullable();
            $table->float("cost")->nullable();
            $table->smallInteger("long")->nullable();

//            $table->integer("CS_SVKEY");

//            $table->integer("CS_SUBCODE1")->nullable();
//            $table->integer("CS_SUBCODE2")->nullable();
//
//            $table->integer("CS_PRKEY");
//            $table->string("CS_WEEK"); // default 7 ?
//            $table->float("CS_COSTNETTO");
//            $table->float("CS_COST")->nullable();
//            $table->smallInteger("CS_DISCOUNT")->nullable();
//            $table->smallInteger("CS_TYPE")->nullable();
//            $table->integer("CS_CREATOR");
//            $table->string("CS_RATE"); // default 2?
//            $table->dateTime("CS_UPDDATE")->nullable();
//            $table->smallInteger("CS_LONG")->nullable();
//            $table->smallInteger("CS_BYDAY");
//            $table->smallInteger("CS_FIRSTDAYNETTO")->nullable();
//            $table->smallInteger("CS_FIRSTDAYBRUTTO")->nullable();
//            $table->float("CS_PROFIT")->nullable();
//            $table->timestamp("ROWID");
//            $table->integer("CS_CINNUM")->nullable();
//            $table->smallInteger("CS_TypeCalc")->nullable();
//            $table->dateTime("cs_DateSellBeg")->nullable();
//            $table->dateTime("cs_DateSellEnd")->nullable();
//            $table->dateTime("CS_CHECKINDATEEND")->nullable();
//            $table->smallInteger("CS_LONGMIN")->nullable();
//            $table->smallInteger("CS_TypeDivision")->nullable();
//            $table->string("CS_UPDUSER")->nullable();
//            $table->integer("CS_TRFId");
//            $table->integer("CS_COID")->nullable();

//    CS_ID integer IDENTITY(1;1) NOT for REPLICATION;
//                                        CS_CHECKINDATEBEG datetime ->nullable();


//            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_costs');
    }
};
