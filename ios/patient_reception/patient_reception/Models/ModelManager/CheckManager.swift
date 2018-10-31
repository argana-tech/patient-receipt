//
//  CheckManager.swift
//  patient_reception
//
//  Created by nishimura on 2018/09/27.
//  Copyright © 2018年 jp.ac.tottori-u.med.hosp. All rights reserved.
//

import UIKit
import Alamofire
import Alamofire_SwiftyJSON
import SwiftyJSON


class CheckManager: AbstractManager<CheckObject> {

    // 端末登録チェック
    // 成功時にmessageで登録前、登録後のコメントを出力します
    func postRegister (check:CheckObject, complitionHandler:@escaping (CheckObject) -> Void) {
        let params: [String:AnyObject] = [
            AbstractObject.Request.unigueId.rawValue : check.uniqueId as AnyObject,
        ]
        let url:String = Server.getUrl(api: .check(action: .register))
        request(url, method: .post, params: params, complitionHandler: complitionHandler)
        
//        Alamofire.request(url, method: .post, parameters: params).responseSwiftyJSON { (responce) in
//
//            let checkObject = CheckObject()
//            checkObject.setData(response: responce)
//
//            complitionHandler(checkObject)
//        }
    }
    
    // 受付状況可否チェック
    // 成功時にmessageで受付前、受付中、受付後のコメントを出力します
    func postTime (check:CheckObject, complitionHandler:@escaping (CheckObject) -> Void) {
        let params: [String:AnyObject] = [
            AbstractObject.Request.unigueId.rawValue : check.uniqueId as AnyObject,
            ]
        let url:String = Server.getUrl(api: .check(action: .time))
        request(url, method: .post, params: params, complitionHandler: complitionHandler)
//        Alamofire.request(url, method: .post, parameters: params).responseSwiftyJSON { (response) in
//
//            let checkResult = CheckObject()
//            checkResult.setData(response: response)
//
//            complitionHandler(checkResult)
//
//        }
    }
    
    override func createObject() -> AbstractObject {
        return CheckObject()
    }
}
