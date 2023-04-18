[{assign var="oCaptcha" value=$oView->getCaptcha()}]
<input type="hidden" name="c_mach" value="[{$oCaptcha->getHash()}]"/>

<div class="form-group row verify">
    <label class="req [{$labelCssClass}]" for="c_mac">[{oxmultilang ident="VERIFICATION_CODE"}]</label>

    <div class="[{$inputCssClass}]">
        <div class="input-group">
            <span class="input-group-addon" style="padding-right:15px">
                <img src="[{$oCaptcha->getImageUrl()}]" alt="">
            </span>
            <input type="text" data-fieldsize="verify" name="c_mac" value="" class="form-control js-oxValidate js-oxValidate_notEmpty" required>
        </div>
    </div>
</div>