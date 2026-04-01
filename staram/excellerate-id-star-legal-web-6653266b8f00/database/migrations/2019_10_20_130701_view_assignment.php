<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ViewAssignment extends Migration
{
    private function createFolderShareView()
    {
        DB::statement('DROP VIEW IF EXISTS `folder_share_view`');
        DB::statement("CREATE VIEW folder_share_view AS(
            SELECT 'FOLDER' as type,
                folder_share.id as doc_id,
                folder_share.folder_id,
                NULL as user_id,
                folder_share.folder_name as doc_name,
                folder_share.creator_id as submitter_id,
                NULL as company_name,
                NULL as doc_type,
                NULL as agreement_date,
                NULL as agreement_number,
                NULL as parties,
                NULL as expire_date,
                NULL as remark,
                folder_share.description,
                NULL as attachment,
                folder_share.created_at as date_creation
            FROM `folder_share`)");
    }

    private function createDocShareView()
    {
        DB::statement('DROP VIEW IF EXISTS `doc_share_view`');
        DB::statement("CREATE VIEW doc_share_view AS(
            SELECT 'DOC' as type,
                doc_share.id as doc_id,
                doc_share.folder_id,
                doc_user.user_id,
                doc_share.doc_name,
                doc_share.submitter_id as submitter_id,
                doc_share.company_name,
                doc_share.doc_type,
                doc_share.agreement_date,
                doc_share.agreement_number,
                doc_share.parties,
                doc_share.expire_date,
                doc_share.remark,
                doc_share.description,
                doc_share.attachment,
                doc_share.created_at as date_creation
            FROM `doc_share`
            LEFT OUTER JOIN doc_user on (doc_share.id = doc_user.doc_id))");
    }

    private function createDocRequestView()
    {
        DB::statement('DROP VIEW IF EXISTS `doc_request_view`');
        DB::statement("CREATE VIEW doc_request_view AS(
            SELECT
                doc_request.id, doc_type.type as doc_type, doc_request.approval_type,
                doc_request.purpose, doc_request.proposed_by, doc_request.proposed_date, doc_request.parties, doc_request.description,
                doc_request.commercial_terms, doc_request.transaction_value, doc_request.late_payment_toleration, doc_request.condition_precedent,
                doc_request.termination_terms, doc_request.payment_terms, doc_request.delay_penalty, doc_request.guarantee, doc_request.agreement_terms,
                doc_request.isActive as is_active,
                doc_request.status as status_id, status.right_name as status_name,
                doc_request.nextStatus as next_status_id, next_status.right_name as next_status_name,
                doc_approval.ceo_approved, doc_approval.cfo_approved, doc_approval.bu_approved, doc_approval.legal_approved,
                doc_request.owner_id, owner.fullname as owner_name, owner.avatar as owner_avatar,
                doc_request.last_owner_id, last_owner.fullname as l_owner_name, last_owner.avatar as l_owner_avatar,
                doc_request.requester_id, requester.fullname as requester_name, requester.avatar as requester_avatar,
                doc_request.created_at, doc_request.updated_at
                FROM doc_request
                JOIN doc_type ON doc_type.id = doc_request.doc_type
                JOIN users as requester ON requester.id = doc_request.requester_id
                JOIN rights as status ON status.id = doc_request.status
                JOIN doc_approval ON doc_approval.req_id = doc_request.id
                LEFT OUTER JOIN rights as next_status on next_status.id = doc_request.nextStatus
                LEFT OUTER JOIN users as owner ON owner.id = doc_request.owner_id
                LEFT OUTER JOIN users as last_owner ON last_owner.id = doc_request.last_owner_id)"
            );
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createFolderShareView();
        $this->createDocShareView();
        $this->createDocRequestView();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doc_share_view');
        Schema::dropIfExists('folder_share_view');
        Schema::dropIfExists('doc_request_view');
    }
}
